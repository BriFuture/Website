<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate: 05.19
 * modifydate:05.xx
 * filename: DbLimit.class.php
 * 对访问做出限制，记录限制的次数和行为
*/
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}
class DbLimits {
  const COLUMNS = [];
  private $db;
  private $table_name = 'limits';

  public function __construct() {
    $this->db = Db::getInstance();
  }

  /**
   * 根据用户id或者ip地址的行为返回限制
   * @param  $userid  用户id
   * @param  $ip      远程ip
   * @param  $action  行为
   */
  public function get_limit($userid, $ip, $action) {

  }

  /**
   * 添加受限制的用户，记录限制的次数
   * @param  $userid  用户id
   * @param  $action  行为
   * @param  $period  时期
   * @param  $count   次数
   */
  public function add_user($userid, $action, $period, $count) {

  }

  /**
   * 添加受限制ip，记录受限制的次数
   * @param  $ip      远程ip地址
   * @param  $action  行为
   * @param  $period  时期
   * @param  $count   次数
   */
  public function add_ip($ip, $action, $period, $count) {

  }
}