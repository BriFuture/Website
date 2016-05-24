<?php  
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 05.19
 * table: text
 * filename: DbText.class.php
 * 存储大文本
*/
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}

class DbText {
  const COLUMNS = ['textID', 'UID', 'parentID', 'title', 'content', 'only_text', 'allow_question', 'up_time', 'last_modified', 'category'];
  private $db;
  private $table_name = 'text';

  public function __construct() {
    $this->db = Db::getInstance();
  }



}