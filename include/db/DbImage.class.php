<?php  
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 06.16
 * table: image
 * filename: DbImage.class.php
 * 记录图片 hash 和位置的对应关系
*/
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}

class DbImage {
  // const COLUMNS = ['ID', 'name', 'style', 'create_time', 'permission'];
  private $db;
  const TABLE_NAME = 'image';

  public function __construct() {
    $this->db = Db::getInstance();
  }
  
}