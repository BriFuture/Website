<?php  
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 04.27 not start yet 05.05
 * table: user
 * filename: DbInvitation.class.php
*/
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}
// require_once CORE_PATH.'db/Db.class.php';
class DbInvitation {
  const COLUMNS = [];
  private $db;
  private $mysqli;
  private $table_name = 'invitation';

  public function __construct() {
    $this->db = Db::newInstance();
    $this->mysqli = $this->db->connection();
  }

  public function someCode() {
    
  }

  public function create_invite_code($create) {

  }

  public function check_invite_code() {
    
  }
}