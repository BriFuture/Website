<?php  
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 05.19
 * table: block
 * filename: DbBlock.class.php
 * 记录版块信息
*/
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}

class DbBlock {
  // const COLUMNS = ['ID', 'name', 'style', 'create_time', 'permission'];
  private $db;
  const TABLE_NAME = 'block';

  public function __construct() {
    $this->db = Db::getInstance();
  }
  
}