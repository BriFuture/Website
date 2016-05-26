<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * Db interface
 *
 */
interface DbImpl {
  //==============基础的===========
  /**
   * 
   * 连接数据库
   * @param $fail_handle 错误处理
   */
  // function connect($fail_handle);
  /**
   * 错误处理机制
   * @param $type 错误类型
   * @param $errno 错误行
   * @param $error 错误
   * @param $query 
   */
  function fail_error($type, $errno=null, $error=null, $query=null);
  /**
   * 返回数据库连接
   * @return mysqli
  */
  function connection();
  /**
   * 关闭连接
   */
  function disconnect();
  /**
   * 封装原始mysql语句查询，需要将参数转换成mysql能够识别的形式
   * 由其他函数封装
   * @param $query 查询语句
   */
  function query_raw($query);
  /**
   * 返回为mysql转义的参数，如果$alwaysquote 为true或者不是数字，就在参数两边添加引号
   * 如果参数是数组,返回一组用逗号隔开的转义元素
   * @param arraybrackets 是否使用括号
   */
  function argument_to_mysql($argument, $alwaysquote, $arraybracket=false);

  /**
   * 提供封装好的查询
   */
  function query($query);
  //==========对mysqli_result进行封装==========
  
  /**
   * 返回最后一个修改的值
   */
  function last_insert_id();
  /**
   * 返回影响的行
   */
  function affected_rows();

  /**
   * 取出数据
   * @param $selectspecs 数组，决定取出什么样的数据
   */
  function multi_select($selectspecs);
  function single_select($selectspec);
  /**
   * 返回行数
   */
  static function num_rows($result);
  /**
   * 产生随机的大整数
   */
  static function random_bigint();
  /**
   * 返回嵌套的关联数组
   */
  static function get_all_assoc($result, $key=null, $value=null);
  /**
   * 返回关联数组中的第一个数组
   */
  static function get_one_assoc($result, $allowempty=false);
  /**
   * 返回所有列的值
   */
  static function get_all_value($result);
  /**
   * 返回第一行第一列的值
   */
  static function get_one_value($result, $allowempty=false);
  /**
   * 可选的
   * 列出表
   */
  // function list_tables();
  // function list_tables_lc();
}