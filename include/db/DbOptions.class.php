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
  const TABLE_NAME = 'options';
  private $options;

  public function __construct() {
    $this->db = Db::getInstance();
  }

  /**
   * 添加数据库中的option 选项 
   * @param  $name  option 的名称
   * @param  $value  要设置的值
   * @param  $autoload  bool  是否要自动加载
   */
  private function create($name, $value, $autoload) {
    // 不使用REPLACE 因为REPLACE会将id自动+1
    // $this->db->query_sub('REPLACE options (name, value) VALUES ($, $)', $name, $value);
    $query_str = 'INSERT INTO options (`name`, `value`, `autoload`) VALUES ($, $, $)';
    $this->db->query($query_str, $name, $value, ($autoload ? 'y' : 'n') );
  }

  /**
   * 设置option的值，如果在数据库中没有该选项，就创建一个，如果有就更改该选项
   * @param  $name  option 的名称
   * @param  $value  要设置的值
   * @param  $autoload  bool  是否要自动加载
   * @return  如果创建选项的话，返回最后插入的id值
   *          如果是更新的话，返回影响的行数
   */
  public function set_option($name, $value, $autoload) {
    if(!isset($this->options))
    {
      $this->get_all_options();
    }

    if(array_key_exists($name, $this->options)) 
    {
      $this->update($name, $value, $autoload);
      return $this->db->affected_rows();
    }
    else
    {
      $this->create($name, $value, $autoload);
      return $this->db->last_insert_id();
    }

  }

  /**
   * 更新选项
   * @param  $name      option 的名称
   * @param  $value     要设置的值
   * @param  $autoload  bool  是否要自动加载
   */
  public function update($name, $value, $autoload) {
    $query_str = 'UPDATE `options` SET `value`=$, `autoload`=$ WHERE `name`=$';
    $this->db->query($query_str, $value, $autoload, $name);
  }

  /**
   * 删除
   * @param  要删除项的名字
   */
  public function delete($name) {
    $query_str = 'DELETE FROM `options` WHERE `name`=$';
    $this->db->query($query_str, $name);
  }

  /**
   * 读取数据库中的option的值
   * @param  $name  option 的名称
   * @param  $value  要设置的值
   */
  public function get_option($name) {
    foreach ($this->options as $index => $option) 
    {
      if($option['name'] === $name)
      {
        return $option;
      }
    }
    return false;
  }

  /**
   *
   */
  public function get_column_value($column) {

  }
  /**
   * 读取数据库中的options的所有值
   * @param  $name  option 的名称
   * @param  $value  要设置的值
   * @return  数组
   */
  public function get_all_options() {
    $query_str = 'SELECT `id`, `name`, `value`, `autoload` FROM `options`';
    $result = $this->db->query($query_str);

    $this->options = Db::get_all_assoc($result);
    return $this->options;
  }
}