<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime: 04.xx
 * lastmodifytime: 05.18
 * filename: Base.class.php
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}
require_once INCLUDE_PATH.'config.php';
class Base {

  /**
   * 使用index.php+参数形式
   * http://host.com/index.php?a=1&b=2&c=3
   */
  const URL_FORMAT_INDEX = 0;
  /**
   * 需要.htaccess支持
   * http://host.com/1/2/3
   */
  const URL_FORMAT_SLASH = 1;
  /**
   * 没有index.php 有参数
   * http://host.com/?a=1&
   */
  const URL_FORMAT_PARAM = 2;
  /**
   * 伪静态,需要.htaccess支持
   * http://host.com/a/b/c.html
   */
  const URL_FORMAT_FAKE_STATIC = 4;

  /**
   * url的query关键字
   */
  const URL_LAYER_ACTION = 'action';
  const URL_LAYER_PAGE   = 'page';
  const URL_LAYER_VIEW   = 'view';
  const URL_LAYER_CAT    = 'cat';

  /**
   * 是否暂停报告事件的锁
   */
  private $suspend;

  /**
   * 所有模块
   */
  private $modules;

  public function __construct() {

  }

  /**
   * 重定向，封装header函数
   * @param $url 重定向到$url地址
   * @param $opt 额外的选项
   */
  public function raw_redirect($url, $opt=null) {
    header('Location: '.$url);
    self::base_exit('redirect');
  }

  /**
   * 设置URL格式
   * @param  $url_format
   */
  public function set_url_format($url_format) {
    $configure = new Configure();
    return $configure->modify_config('URL_FORMAT', $url_format);
  }

  /**
   * 分发操作
   */
  public function dispatch() {
    //界面
    $page = new Page();

    //跳转页面
    if ($this->super_post_text('ajax')) {
      //如果是ajax请求
      //将请求转发给ajax

      return;
    }
    if($this->super_get(self::URL_LAYER_ACTION))
    {
      //需要将image单独出来，因为path不同
      //后期需要将连接改到control层
      switch ($this->super_get(self::URL_LAYER_ACTION)) {
        case 'err':
          //转到错误页面
          $err = $this->super_get('err');
          $msg = array(
            'errcode' => $err,
          );
          $page->err($msg);
          break;
        case 'util':
          # code...
          break;
        case self::URL_LAYER_PAGE:
          $page->dispatch(strtolower($this->super_get(self::URL_LAYER_PAGE)), strtolower($this->super_get(self::URL_LAYER_VIEW)));
          break;
        case 'ext':
          $cat = $this->super_get(self::URL_LAYER_CAT);
          if(is_dir($cat) && file_exists($cat.'/index.php'))
          {
            include $cat.'/index.php';
          }
          break;  
        case 'test':
          if(DEBUG_MODE)
          {
            $page = PUBLIC_PATH.$this->super_get(self::URL_LAYER_PAGE).'.phtml';
            include $page;
          }
          else
          {
            //找不到页面
            $msg = array('errcode' => 404);
            $page->err($msg);
          }
          break;
        default:
          $page->dispatch('err');
          break;
      }
    }
    else
    {
      $page->dispatch();
    }
  }

  /**
   * 使用指定的URL格式返回路径
   * @return url path
  */
  public function get_path($request, $params=null) {
    $path = PUBLIC_PATH ;
    // if($request)
    $request=str_replace('.class','',$request);
    switch (self::$url_format) {
      case self::URL_REWITE:
        # code...
        $request=str_replace('.php', '.html', $request);
        $path='/'.$request;
        break;
      case self: URL_FORMAT_INDEX:
      default:
        # code...
        $path=$request;
        break;
    }
    return $path;
  }

  /**
   * 初始化常量
   */
  public function initialize_constants() {

  }

  /**
   * 用json编码
   */
  public function json_encode() {

  }

  /**
   * 用json解码
   */
  public function json_decode() {

  }

  /**
   * 将VERSION字符串转换成float
   * @return float  
   *      版本的数值
   */
  public function version_to_float($version) {
    //float value
    $value = 0.0;
    if(preg_match('/[0-9\.]+/', $version, $matches))
    {
      $parts = explode('.', $matches[0]);
      //权重
      $units=1.0;

      foreach ($parts as $part) {
        $value += min($part, 999) * $units;
        $units/=1000;
      }

      return $value;
    }
  }

  /**
   * 判断当前的php版本是否低于所需的最低版本
   * @return bool 
   *      true  当前php版本较低，无法运行
   *      false 可以运行
   */
  public function php_version_below($version) {
    $minphp  = $this->version_to_float($version);
    $thisphp = $this->version_to_float(phpversion());

    return $minphp && $thisphp && ($minphp > $thisphp); 
  }

  /**
   * 初始化php
   */
  public function initialize_php() {
    if($this->php_version_below('5.4.0'))
      $this->fatal_error('需要php 5.4.0或之后的版本','php版本过低');
    //设置错误级别
    error_reporting(E_ALL);
    //try 关闭魔术引号
    @ini_set('magic_quotes_runtime', 0);
    //字符串的分类与转换
    @setlocale(LC_CTYPE, 'C');
    //设定时区东八区
    if(function_exists('date_default_timezone_set'))
      @date_default_timezone_set('Asia/Shanghai');

    if(ini_get('register_globals'))
    {
      $check_arrays = array('_ENV', '_GET', '_POST', '_COOKIE', '_SERVER', '_FILES', '_REQUEST', '_SESSION');
      //将$check_arrays和 'GLOBALS' 合并后互换键值
      $key_protect  = array_flip(array_merge($check_arrays, array('GLOBALS')));

      foreach ($check_arrays as $check_array) {
        //将$check_array当作变量名
        if(isset(${$check_array}) && is_array(${$check_array}))
          foreach ($check_array as $check_key => $check_value) {
            if(isset($key_protect[$check_key]))
              $this->fatal_error('超全局变量不允许覆盖');
            else
              unset($GLOBALS[$check_key]);
          }
      }
    }

  }

  /**
   * 初始化模块配置
   */
  public function initialize_modularity() {

  }

  /**
   * 注册核心模块
   */
  public function register_core_modules() {

  }

  /**
   * 注册层
   */
  public function register_layer() {

  }

  /**
   * 从文件中读取内容
   * 把字符串作为PHP代码执行，不能读取'<'开头的文件，报错不识别
   * @param $eval
   * @param $filename
   */
  public function eval_from_file($eval, $filename) {

  }

  /**
   * 自定义的函数调用
   * 是因为弱语言类型的php不能直接支持多态和重载，所以使用分支形式来进行virtual invoke
   * @param  $function 被调用的函数
   * @param  $args     参数
   */
  public function base_call($function, $args) {
    switch(count($args)) {
      case 0:
        return $function();
      case 1:
        return $function($args[0]);
      case 2:
        return $function($args[0], $args[1]);
      case 3:
        return $function($args[0], $args[1], $args[2]);
      case 4:
        return $function($args[0], $args[1], $args[2], $args[3]);
      case 5:
        return $function($args[0], $args[1], $args[2], $args[3], $args[4]);
      case 6:
        return $function($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);     
    }
  }

  /**
   * 退出，向观察者报告状态
   * @param  $reason  退出的原因
   */
  public function base_exit($reason=null) {
    $this->report_process('shutdown', $reason);
    exit;
  }

  /**
   * @return  返回所有模块的所有信息
   */
  public function list_modules_info() {
    return $this->modules;
  }

  /**
   * 注册并添加模块
   * @param  $type  模块类型
   * @param  $name  名字
   */
  public function register_module($type, $include, $class, $name) {
    if(isset($this->modules[$type][$name]))
    {
      $previous = $this->modules[$type][$name];
      $this->fatal_error('A '.$type.' module named '.$name.' already exists. Please check there are no duplicate plugins. '.
        "\n\nModule 1: ".$previous['directory'].$previous['include']);
    }

    $this->modules[$type][$name] = array(
      // 'directory' => $directory,
      // 'urltoroot' => $urltoroot,
      'include'   => $include,
      'class'     => $class,
    );
  }


  /**
   * @return 列出所有模块的类型
   */
  public function list_modules_type() {
    return array_keys($this->list_modules_info());
  }

  /**
   * 列出某种类型的所有模块
   * @param  $type  模块类型
   * @return 
   *      如果存在某种类型的模块，则返回包含所有模型名字的数组，否则返回一个空数组
   */
  public function list_modules($type) {
    $modules = $this->list_modules_info();
    return is_array(@$modules[$type]) ? array_keys($modules[$type]) : array();
  }

  /**
   * 获取特定模块的信息
   * @param  $type  模块类型
   * @param  $name  模块名字
   */
  public function get_module_info($type, $name) {
    $modules = $this->list_modules_info();
    return isset($modules[$type][$name]) ? $modules[$type][$name] : null;
  }

  /**
   * 加载模块
   * @param  $type  模块的类型
   * @param  $method  方法
   */
  public function load_modules_with($type, $method) {
    $modules = array();

    $trynames = $this->list_modules($type);

    foreach ($trynames as $tryname) {
      //加载模块
      $module = $this->load_module($type, $tryname);

      //如果模块的方法存在
      if(method_exists($module, $method))
      {
        $modules[$tryname] = $module;
      }
    }

    return $modules;
  }

  /**
   * 用$method 加载所有模块
   * @param $method  加载的方式
   */
  public function load_all_modules_with($method) {
    $modules = array();
    //列出模块的信息
    $regmodules = $this->list_modules_info();
    foreach($regmodules as $moduletype => $modulesinfo) {
      foreach($modulesinfo as $modulename => $moduleinfo) {
        $module = $this->load_module($moduletype, $modulename);

        if(method_exists($module, $method))
        {
          $modules[$modulename] = $module;
        }
      }
    }

    return $modules;
  }

  /**
   * 加载指定模块
   * @param $type  模块类型
   * @param $name  模块名
   */
  public function load_module($type, $name) {
    $module = @$this->modules[$type][$name];

    if(is_array($module))
    {
      if (isset($module['object'])) 
      {
        //如果有object
        return $module['object'];
      }

      if(strlen(@$module['include']))
      {
        require_once $module['directory'].$module['include'];
      }

      if(strlen(@$module['class']))
      {
        //new 一个 module对象
        $object = new $module['class'];

        if(method_exists($object, 'load_module'))
        {
          $object->load_module($module['directory'], $module['urltoroot'], $type, $name);
        }

        $this->modules[$type][$name]['object'] = $object;
        return $object;
      }
    }
  }

  /**
   * 返回html
   */
  public function get_html($string, $multiline=false) {

  }

  /**
   * 过滤html
   */
  public function sanitize_html($html, $links_new_window) {

  }

  /**
   * 将xml中的特定字符去掉
   * @param  $string  特定字符
   */
  public function get_xml($string) {
    return htmlspecialchars(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', (string)$string ));
  }

  /**
   * 将JavaScript中的相应字符替换 并返回
   * @param  $value  
   *      如果是boolean的话返回 true or false
   *      如果是数字的话返回数字
   *      如果强制引号，需要转换
   * @param  $force_quotes
   *      是否强制引号  
   */
  public function get_js($value, $force_quotes=false) {
    $boolean = is_bool($value);
    if($boolean)
    {
      $value = $value ? 'true' : 'false';
    }
    if((is_numeric($value) || $boolean) && !$force_quotes) 
    {
      return $value;
    }
    else
    {
      return ".".strtr($value, array(
        "'"  => "\\'",
        "/"  => "\\/",
        "\\" => "\\\\",
        "\n" => "\\n",
        "\r" => "\\n",
        ))."'";
    }
  }

  /**
   * 封装$_GET
   * @param $field  GET数组的键
   * @return 返回GET中$filed的值  
   */
  static public function super_get($field) {
    return isset($_GET[$field]) ? $_GET[$field] : null;
  }

  /**
   * 封装$_POST
   * 将值中的\r\n换成\n
   * @param $field  POST数组的键
   * @return 返回POST中$filed的作为文本的值 
   */
  static public function super_post_text($field) {
    return isset($_POST[$field]) ? preg_replace('/\r\n?/', "\n", trim($_POST[$field])) : null;
  }

  /**
   * 封装$_POST
   * 将$_POST中所有的值剥去空白符后输出
   * @param $field  POST数组的键
   * @return 将POST中$filed的值作为数组返回 
   */
  static public function super_post_array($filed) {
    //如果$_POST没有相应的值或者不是数组
    if(!isset($_POST[$field]) || !is_array($_POST[$field]))
    {
      return null;
    }

    $result = array();
    foreach ($_POST[$filed] as $key => $value) 
    {
      $result[$key] = preg_replace('/\r|\r\n?/', "\n", trim($value));
    }

    return $result;
  }

  /**
   * 封装$_SERVER
   * @return 返回访问者的ip
   */
  public static function get_remote_ip() {
    return @$_SERVER['REMOTE_ADDR'];
  }

  /**
   * 检查HTTP请求是否超出了php变量的最大限度
   * @return  bool
   *      true  超过    false  没有超过
   */
  public function post_limit_exceeded() {
    if(in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT')) && empty($_POST) && empty($_FILES))
    {
      $postmaxsize = ini_get('post_max_size');
      $unit = substr($postmaxsize, -1);
      //如果单位不是数字的话
      if(!is_numeric($unit)) {
        //将postmaxsize的最后一个字符去掉
        substr($postmaxsize, 0, -1);
      }
      switch (strtoupper($unit)) {
        case 'G':
          $postmaxsize *= 1024;
        case 'M':
          $postmaxsize *= 1024;
        case 'K':
          $postmaxsize *= 1024;
      }
    }
    return $_SERVER['CONTENT_LENGTH'] > $postmaxsize;
  }

  /**
   * @return bool
   *      true  是POST请求
   *      false 不是POST请求
   */
  public function is_http_post() {
    return ($_SERVER['REQUEST_METHOD'] == 'POST') || !empty($_POST);
  }

  /**
   * 判断是否是桌面浏览器而不是搜索引擎
   * @return bool
   *      true  是    false  不是
   */
  public function is_desktop_probably() {
    //浏览器标识
    $explore = array(
      'MSIE', 'Firefox', 'Chrome', 'Safari', 'Opera', 'Gecko', 'MIDP', 'PLAYSTATION', 'Teleca',
      'BlackBerry', 'UP.Browser', 'Polaris', 'MAUI_WAP_Browser', 'iPad', 'iPhone', 'iPod'
    );
    return (strlen($useragent) == 0); #|| ($explore);
  }

  /**
   * 是否是移动端浏览器
   * @return bool
   *      true  是    false  不是
   */
  public function is_mobile_probably() {

  }

  /**
   *
   */
  public function lang($identifier) {

  }

  /**
   *
   */
  public function lang_sub($identifier, $textparam) {

  }

  /**
   * 
   */
  public function get_request_map() {

  }

  /**
   * 解析URL 
   * @param  $url  
   */
  public function retrieve_url($url) {
    $contents = @file_get_contents($url);

    if(!strlen($contents) && function_exists('curl_exec'))
    {
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      $contents = @curl_exec($curl);
      curl_close($curl);
    }
  }

  /**
   * 对数据库的数据进行缓存，缓存后直接读取某些数据
   * 如果设置了$value，就更新$name的值
   * @param  $name   变量名
   * @param  $value  相应的值
   */
  public function opt($name, $value=null) {
    global $options_cache;

    //如果没有设置$value 就是取出相应的值
    if(!isset($value) && isset($options_cache[$name]))
    {
      return $options_cache[$name];
    }

    $option = Factory::getObject('Options');
    //设置相应的键值
    if(isset($value))
    {
      $option->set_option($name, $value);
    }

    $options = $option->get_options(array($name));

    return $options[$name];
  }

  /**
   * 输出调试信息
   * @param $var  需要调试的内容
   */
  public function debug($var) {
    $debug = "\n<pre>".($var === null ? 'NULL' : print_r($var, true))."</pre>\n";
    echo nl2br($debug);
  }
  
  /**
   * 暂停报告事件
   * @param  $suspend  是否暂停，默认为暂停
   */
  public function suspend_event_report($suspend=true) {
    $this->suspend += ($suspend ? 1 : -1);
    return $this->suspend;
  }

  /**
   * 报告事件
   * @param  $event
   * @param  $userid
   * @param  $handle
   * @param  $cookieid
   * @param  $params
   */
  public function report_event($event, $userid, $handle, $cookieid, $params=array()) {
    if($this->suspend)
    {
      //暂停报告事件
      return;
    }

    $event_modules = $this->load_modules_with('event', 'process_event');

    foreach($event_modules as $event_module) {
      $event_module->process_event($event, $userid, $handle, $cookieid, $params);
    }
  }

  /**
   * 报告进程，向观察者发出通知
   * 使用观察者模式
   * @param  $method  报告进程所用的方法
   * @param  可以有多个参数
   */
  public function report_process($method) {
    if($this->suspend)
    {
      //暂停报告事件
      return;
    }
    //暂停其它事件，防止互斥，上锁
    $this->suspend_event_report();

    $args = func_get_args();
    $args = array_slice($args, 1);

    $process_modules = $this->load_modules_with('process', $method);
    foreach($process_modules as $process_module) {
      call_user_func_array(array($process_module, $method), $args);
    }
    //释放锁
    $this->suspend_event_report(false);
  }

  /**
   * 报告致命的错误
   * @param  $error  错误的内容，如果是数字的话，就直接数字对应显示http错误码错误
   * @param  $cTitle 错误内容的标题
   * @param  $title  网页标题
   */
  public function fatal_error($error, $cTitle=null, $title='发生了一个严重的错误') {
    //如果仅仅是数字的话，直接传递errcode就行
    if(is_numeric($error))
    {
      $msg = array(
        'errcode' => $error,
      ); 
    }
    else
    {
      $msg = array(
        'title'    => $title,
        'cTitle'   => $cTitle,
        'content'  => $error,
      );
    }

    $err = new Err();
    $err->view_err($msg);
    exit;
  }

  /**
   * date:0428
   * 
   * 用来输出html内容
   * @param $type指定类型 为meta 或者link或者script或者raw
   *        当$type为raw时，将$addition原样输出
   * @param $addtion输出其它指定的信息
  */
  public function html_content($type, $addition) {
    if($type === 'raw')
    {
      $content = $addition;
    }
    else
    {
      switch ($type) {
        case 'js':
          $content = '<script type="text/javascript" src="'.$addition['src'].'"></script>';
          break;
        case 'css':
          $content = '<link rel="stylesheet" href="'.$addition['href'].'" />';
          break;
        // case 'script':
        //   $content = '<script type="text/javascript" src="'.$addtion['src'].'"></script>';
        //   // <script type="text/javascript" src="/js/index.js"></script>
        //   break;
        case 'meta':
        case 'link':
        // case 'img'://不直接使用地址，改用php绘制
          $content = '<'.$type;
          if(is_array($addition))
            foreach ($addition as $key => $value) {
              $content .= ' '.$key.'="'.$value.'" ';
            }
          $content .= $type.' />';
          break;
        case 'img':
          $content = '<img';
          if(is_array($addition))
            foreach ($addition as $key => $value) {
              $content .= ' '.$key.'="'.$value.'" ';
            }
          $content .= 'img />';
          break;
      }
    } 
    echo $content;
  }
}