<?php  
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 04.xx
 * filename: Db.class.php
 * 操作数据库的基础
 * 所有的数据操作都应该引用该文件
 * 包含对数据库结果的处理
*/
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}
//读取配置
require_once CORE_PATH.'db_config.php';
require_once CONTROL_PATH.'Base.class.php';
//实现接口
require_once DB_PATH.'Db.impl.php';

class Db implements DbImpl{
  /**
   * 允许连接
   * 维护时关闭
   */
  private $allow_connect;
  /**
   * 是否持久化连接
   */
  private $persistent;
  /**
   * 错误处理
   */
  private $fail_handle;
  // private $connect;
  /**
   * 保存mysqli连接
   */
  private $connection = null;

  /**
   * 单例模式使用
   */
  private static $db;
  /**
   * 将mysqli对象封装
  */
  private function __construct($persistent=true) {
    $this->persistent = $persistent;
    $this->allow_connect=ALLOW_CONNECT_DB;
    // 从配置文件中读取是否允许连接数据库
    // $this->allow_connect = $connect;
  }
  public function __destruct() {
    $this->disconnect();
  }

  /**
   * 获取Db实例
   */
  public static function getInstance() {
    if(is_null(self::$db)) {
      self::$db = new Db();
    }
    return self::$db;
  }
  /**
   * 
   * 连接数据库
   * @param $fail_handle 错误处理
   */
  private function connect($fail_handle) {
    if($this->persistent)
    {
      $db = new mysqli('P:'.HOST, USER, PASSWD, DATABASE);
    }
    else
    {
      $db = new mysqli(HOST, USER, PASSWD, DATABASE);
    }
    if($db->connect_error)
    {
      // die('Connect Error ('.$db->connect_errno.')'.$db->connect_error);
      $this->fail_error('connect', $db->connect_errno, $db->connect_error);
    }
    if(!$db->set_charset('utf8'))
      $this->fail_error('set_charset', $db->connect_errno, $db->connect_error);
    // echo 'Success... '.$mysqli->$host_info.'\n';
    //==============
    $base = Factory::getObject('Base');
    $base->report_process('db_connected');
    $this->connection = $db;
  }

  
  public function is_connect_allow() {
    return $this->allow_connect;
  }
  
  /**
   * 错误处理机制
   * @param $type 错误类型
   * @param $errno 错误行
   * @param $error 错误
   * @param $query 
   */
  public function fail_error($type, $errno=null, $error=null, $query=null) {
    @error_log('PHP mysql '.$type.' error '.$errno,': '.$error);
    global $fail_handle;
    if(function_exists($fail_handle))
    {
      $fail_handle($type, $errno, $error, $query);
    }
    else
    {
      $errors = 'Database '.htmlspecialchars($type.' errno '.$errno).'<p>'.nl2br(htmlspecialchars($error."\n".$query)).'</p>';
      $base   = Factory::getObject('Base');
      $base->fatal_error($errors, '数据库错误');
      // exit();
    }
  }

  /**
   * 返回数据库连接
   * @return mysqli对象
  */
  public function connection() {
    //如果没有实例化mysqli
    if($this->allow_connect && !($this->connection instanceof mysqli)) 
    {
      $this->connect(null);
      if(!($this->connection instanceof mysqli))
      {
        $base = Factory::getObject('Base');
        $base->fatal_error('Failed to connect to database');
      }
    }
    return $this->connection;
  }

  /**
   * 关闭连接
   */
  public function disconnect() {
    //如果已经建立连接，关闭
    if($this->connection instanceof mysqli) 
    {
      $base = Factory::getObject('Base');
      $base->report_process('db_disconnect');
      if(!$this->persistent)
      {
        if(!$this->connection->close())
          $base = Factory::getObject('Base');
          $base->fatal_error('Database disconnect failed');
      }
    }
    //指向空对象
    $this->connection = null;
  }

  /**
   * 封装原始mysql语句查询，
   * 如果直接使用该函数，需要调用argument_to_mysql()将参数转换成mysql语句形式
   * 由其他函数封装
   * @param $query 查询语句
   */
  public function query_raw($query) {
    //调试性能
    if(DEBUG_PERFORMANCE) 
    {
      //计时
      $start_time = array_sum(explode(' ', microtime()));
      $result     = $this->query_execute($query);
      $end_time   = array_sum(explode(' ', microtime()));
      $used_time  = $end_time - $start_time;

      $gotrows    = $gotcolumns = null;
      if($result instanceof mysqli_result)
      {
        $gotrows    = $result->num_rows;
        $gotcolumns = $result->field_count;
      }
      //将查询的语句和得到的结果记录
      // 
    }
    else
    {
      $result = $this->query_execute($query);  //执行QUERY
    }

    //如果查询出错
    if($result === false) 
    {
      $this->connection();
      $this->fail_error('query', $this->connection()->errno, $this->connection()->error, $query);
    }
    return $result;
  }

  /**
   * 执行查询，最底层的操作，当数据库出现死锁时自动重新连接
   * @param $query 查询语句
   */
  public function query_execute($query) {
    $result = null;
    for($attempt = 0; $attempt < 100; $attempt++)
    {
      $result = $this->connection()->query($query);
      
      //访问数据库出错，等待一段时间
      if($result === false && $this->connection()->errno == 1213)
        usleep(1000);
      else
        break;
    }
    return $result;
  }

  /**
   * 用数据库对象mysqli转义字符
   */
  public function escape_string($string) {
    return $this->connection()->real_escape_string($string);
  }

  /**
   * 返回为mysql转义的参数，如果$alwaysquote 为true或者不是数字，就在参数两边添加引号
   * 如果参数是数组,返回一组用逗号隔开的转义元素
   * @param  $argument  查询语句
   * @param  $alwaysquote  加上引号
   * @param  $arraybrackets 是否使用括号
   * @return  返回转义后的查询语句
   */
  public function argument_to_mysql($argument, $alwaysquote, $arraybracket=false) {
    if(is_array($argument))   //如果是数组的话
    {
      $parts=array();

      foreach ($argument as $subargument) {
        //将每个部分都转换成mysql参数
        $parts[] = $this->argument_to_mysql($subargument, $alwaysquote, true);
        //使用括号
        if($arraybracket)
          $result = '('.implode(',', $parts).')';
        else
          $result = implode(',', $parts);
      }
    }
    elseif (isset($argument))
    {
      //不是数字，添加单引号
      if($alwaysquote || !is_numeric($argument))
      {
        $result = "'".$this->escape_string($argument)."'";
      }
      else
      {
        $result = $this->escape_string($argument);
      }
    }
    else
    {
      //将$result设置成mysql里的NULL
      $result = 'NULL';
    }
      
    return $result;
    
  }

  /**
   * 将占位符替代为相应的值，封装了mysqli_to_mysql()并转为MYSQL语句
   * $替换成相应的字符串，加上引号
   * #替换成数字，并且不加引号
   * @param  $query  查询语句
   * @param  $arguments  需要替换的参数
   */
  public function substitude($query, $arguments) {
    if(!is_array($arguments))
      return $query;

    //参数个数
    $countargs = count($arguments);
    $offset = 0;

    for($argument = 0; $argument < $countargs; $argument++) {
      //记录第一次找到的位移
      $stringpos = strpos($query, '$', $offset);
      $numberpos = strpos($query, '#', $offset);
      //如果没有要替换的字符串或者要替换数字并且是先替换数字
      if($stringpos === false || ($numberpos !== false && $numberpos < $stringpos))
      {
        $alwaysquote = false;
        $position = $numberpos;
      } 
      else 
      {
        //加引号
        $alwaysquote = true;
        $position = $stringpos;
      }
      //如果找到的位置不是数字，报错
      if(!is_numeric($position))
      {
        $base = Factory::getObject('Base');
        $base->fatal_error('Insufficient parameters in query: '. $query);
      }
      //换成mysql语句
      $value = argument_to_mysql($arguments[$argument], $alwaysquote);
      //用$value替换掉$和#符号，1表示替换，0表示插入
      $query = substr_replace($query, $value, $position, 1);
      $offset = $position + strlen($value);
    }
    return $query;
  }

  /**
   * 其它对象想要查询数据库，应该使用query()函数
   * 封装了query_raw()和substitude() 查询简单
   * @param $query 可以是多个参数
   *  
   */
  public function query($query) {
    //所有的参数，数组形式
    $funcargs = func_get_args();
    //取第一个参数之后的参数
    return $this->query_raw($this->substitude($query, array_slice($funcargs, 1)));
  }

  public function last_insert_id() {
    return $this->connection()->last_insert_id;
  }

  public function affected_rows() {
    return $this->connection()->affected_rows;
  }

  public static function num_rows($result) {
    if($result instanceof mysqli)
      return $result->num_rows;
    return 0;
  }

  /**
   * 
   * @return  返回随机生成的大整数
   */
  public static function random_bigint() {
    //按格式输出大整数
    return sprintf('%d%06d%06d', mt_rand(1,18446743), mt_rand(0,999999), mt_rand(0,999999));
  }

  /**
   * 取出指定的列
   * 自动构造mysql语句，并封装query_raw查询。
   * 不再需要每个类都写mysql查询selcet语句了，而是用数组的形式给出需要查询的列以及列的别名（key）
   */
  public function single_select($selectspec) {
    //构造query语句
    $query = 'SELECT ';
    foreach ($selectspec['columns'] as $columnas => $columnfrom) 
      $query .= $columnfrom.(is_int($columnas) ? '' :(' AS '.$columnas)).', ';
    // print_r($query);

    //添加条数限制
    $limit='';
    if(!is_null(@$selectspec['limit'])) 
    {
      if(is_array($selectspec['limit']) && is_numeric($selectspec['limit'][0]) && is_numeric($selectspec['limit'][1]))
        $limit = ' LIMIT '.$selectspec['limit'][0].','.$selectspec['limit'][1];
      elseif(is_numeric($selectspec['limit']))
        $limit = ' LIMIT '.$selectspec['limit'];
    }
    // echo 'limit: '.$limit;

    //转义并转化为MYSQL语句,去掉最后的', ' 并且加上 FROM 语句 
    $mysqlstr = $this->substitude(
      substr($query, 0, -2).
      (strlen(@$selectspec['source']) ? (' FROM '.$selectspec['source']) : '').$limit
      ,
      @$selectspec['arguments']
    );

    // echo '<br>mysqlstr: ';
    // print_r($mysqlstr);

    //得到原始的结果
    $resultraw = $this->query_raw($mysqlstr);
    // echo '<br>';
    // print_r($resultraw);

    //数据
    $results = $this->get_all_assoc($resultraw, @$selectspec['arraykey']);
    // echo '<br>';
    // print_r($results);
    return $results;
  }

  /**
   * data: 04.21
   * 
   * 还不清楚如何使用
   */
  public function multi_select($selectspecs) {
    //没有参数
    if(!count($selectspecs))
    {
      return array();
    }
    //参数个数小于1
    if(count($selectspecs) <= 1)
    {
      $outresults = array();

      foreach($selectspecs as $skey => $selectspec) {
        $outresults[$skey] = single_select($selectspec); 
      }

      return $outresults;
    }
    //循环每一个参数
    foreach($selectspecs as $skey => $selectspec) {
      $selectspecs[$skey]['outcolumns']=array();
      $selectspecs[$skey]['autocolumn']=array();
      //指定列
      foreach ($selectspec['columns'] as $columns => $columnfrom) {
        if(is_int($columns)) 
        {
          $periodpos = strpos($columnfrom, ',');
          $columnas = is_numeric($periodpos) ? substr($columnfrom, $periodpos+1): $columnfrom;
          $selectspecs[$selectspec]['autocolumn'][$columnas] = true;
        }

        if(isset($selectspecs[$skey]['outcolumns'][$columnas]))
        {
          $base = Factory::getObject('Base');
          $base->fatal_error('Duplicate column name in multi_select()');
        }

        $selectspecs[$skey]['outcolumns'][$columnas] = $columnfrom;
      }

      if(isset($selectspec['arraykey']))
        if(!isset($selectspecs[$skey]['outcolumns'][$selectspec['arraykey']]))
          $base = Factory::getObject('Base');
          $base->fatal_error('Used arraykey not in columns in multi_select()');

      if(isset($selectspec['arrayvalue']))
        if(!isset($selectspecs[$skey]['outcolumns'][$selectspec['arrayvalue']]))
          $base = Factory::getObject('Base');
          $base->fatal_error('Used arrayvalue not in columns in multi_select()');
    }
    //
    $outcolumns=array();
    foreach($selectspecs as $selectspec)
      $outcolumns=array_unique(array_merge($outcolumns,array_keys($selectspec['outcolumns'])));
    //query语句
    $query='';
    foreach($selectspecs as $selectkey => $selectspec) {
      $subquery="(SELECT '".escape_string($selectkey)."'".(empty($query) ? 'AS selectkey' : '');
      foreach ($$outcolumns as $selectkey => $selectspec) {
        $subquery=', '.(isset($selectspec['outcolumns'][$columnas])?$selectspec['outcolumns'] : 'NULL');
        if(empty($query) && !isset($selectspec['autocolumn'][$columnas]))
          $subquery.=' AS '.$columns;
      }

      if(strlen(@$selectspec['source']))
        $subquery.=' FROM '.$selectspec['source'];

      $subquery.=')';
      if(strlen($query))
        $query.=' UNION ALL ';

      $query.=substitude($subquery, @$selectspec['arguments']);
    }
  }
   /**
    * @param $result  mysqli result
    * @param $key     指定嵌套数组的键
    * @param $value   显示$value列的值
    * 未指定$key, $value时返回嵌套的关联数组
    * 指定$key时， 将$key对应的值作为嵌套数组的键
    * 未指定$key但指定$value时， 返回指定$value列的值
    * $key $value不能为数组
   */
  static function get_all_assoc($result, $key=null, $value=null) {
    if(!($result instanceof mysqli_result))
    {
      $base = Factory::getObject('Base');
      $base->fatal_error('Reading assocs from invalid result');
      return;
    }

    $assocs = array();
    while($assoc = $result->fetch_assoc())
    {
      // print_r($assoc);
      // echo '<br>'.$key;

      //如果指定了$key  则将返回的每一个结果集中的第$key列的值作为嵌套数组的键
      //如果指定了$value列， 将返回结果集中的第$value列 
      if(isset($key))
      {
        $assocs[$assoc[$key]] = isset($value) ? $assoc[$value] : $assoc;
      }
      else 
      {
        $assocs[] = isset($value) ? $assoc[$value] : $assoc;
      }
    }
 
    // echo '<br>====<br>';
    // print_r($assocs);
    return $assocs;
  }

  static function get_one_assoc($result, $allowempty=false) {
    if(!($result instanceof mysqli_result))
    {
      $base = Factory::getObject('Base');
      $base->fatal_error('Reading one assoc from invalid result');
      return;
    }  

    $assoc = $result->fetch_assoc();

    if(is_array(($assoc)))
    {
      return $assoc;
    }
    if($allowempty)
    {
      return null;
    }
    else
    {
      $base = Factory::getObject('Base');
      $base->fatal_error('Reading one assoc from empty result');
    }
  }

  static function get_all_value($result) {
    if(!($result instanceof mysqli_result)) 
    {
      $base = Factory::getObject('Base');
      $base->fatal_error('Reading values from invalid result');
      return;
    }
    $output = array();

    while($row = $result->fetch_row()) {
      $output[] = $row[0];
    }

    return $output;
  }

  static function get_one_value($result, $allowempty=false) {
    if(!($result instanceof mysqli_result)) 
    {
      $base = Factory::getObject('Base');
      $base->fatal_error('Reading one value from invalid result');
      return;
    }

    $row = $result->fetch_row();
    if(is_array($row))
    {
      return $row[0];
    }
    if($allowempty)
    {
      return null;
    }
    else
    {
      $base = Factory::getObject('Base');
      $base->fatal_error('Reading one value from empty results');
    }
  }

  /**
   * @param  $name
   * @param  $value
   */
  public function set_option($name, $value) {

  }

  /**
   * @deprecated 仅作为测试使用
  */
  private function testDB() {
    $link = mysql_connect(HOST, USER, PASSWD)
        or die('Could not connect: ' . mysql_error());
    // echo 'Connected successfully';
    mysql_select_db(DATABASE) or die('Could not select database');

    // 执行 SQL 查询
    $query = 'SELECT * FROM view_info';
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

    // 释放结果集
    mysql_free_result($result);

    // 关闭连接
    mysql_close($link);
  }
}
