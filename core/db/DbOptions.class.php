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
  const COLUMNS=['title', 'content'];
  /**
   * 数据库对象
   */
  private $db;
  private $table_name = 'options';

  public function __construct() {
    $this->db = Db::newInstance();
  }

  /**
   * 设置数据库中的option $name 为$value
   * @param  $name  option 的名称
   * @param  $value  要设置的值
   */
  public function set_option($name, $value) {
    $this->db->query_sub('REPLACE options (title, content) VALUES ($, $)', $name, $value);
  }
}