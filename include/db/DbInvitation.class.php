<?php  
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 04.27 not start yet 05.05
 * table: Invitation
 * filename: DbInvitation.class.php
*/
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}

class DbInvitation {
  // const COLUMNS = [];
  private $db;
  const TABLE_NAME = 'invitation';

  public function __construct() {
    $this->db = Db::getInstance();
  }

  public function someCode() {
    
  }

  public function create_invite_code($create) {

  }

  public function check_invite_code() {
    
  }
}