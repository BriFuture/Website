<?php
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 05.06
 * filename: DbOptions.class.php
 * 数据库中保存的网页相关选项
*/
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}

class DbOptions {
  // const COLUMNS=['id', 'name', 'value', 'autoload'];
  /**
   * 数据库对象
   */
  private $db;
  private $table_name = 'options';
  private $options;

  public function __construct() {
    $this->db = Db::getInstance();
  }

  /**
   * 添加数据库中的option 选项 
   *  $name 为$value
   * @param  $name  option 的名称
   * @param  $value  要设置的值
   * @param  $autoload  bool  是否要自动加载
   * @return  返回最后插入的id值
   */
  public function create_option($name, $value, $autoload) {
    // 不使用REPLACE 因为REPLACE会将id自动+1
    // $this->db->query_sub('REPLACE options (name, value) VALUES ($, $)', $name, $value);
    $query = 'INSERT INTO options (`name`, `value`, `autoload`) VALUES ($, $, $)';
    $this->db->query($query, $name, $value, ($autoload ? 'yes' : 'no') );
    return $this->db->last_insert_id();
  }

  /**
   * 读取数据库中的option的值
   * @param  $name  option 的名称
   * @param  $value  要设置的值
   */
  public function get_option($name, $value) {
    
  }

  /**
   * 读取数据库中的options的所有值
   * @param  $name  option 的名称
   * @param  $value  要设置的值
   * @return  数组
   */
  public function get_all_options() {
    $query = 'SELECT `id`, `name`, `value`, `autoload` FROM `options`';
    $result = $this->db->query($query);

    $this->options = $this->db->get_all_assoc($result);
    // print_r($res);
    return $this->options;
  }
}