<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime: 05.06
 * lastmodifytime: xx
 * filename: DbCookies.class.php
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class DbCookies {

  const COLUMNS=['cookieid', 'created', 'createip', 'updated', 'updateip'];
  private $db;
  const TABLE_NAME = 'cookies';

  public function __construct() {
    $this->db = Db::getInstance();
  }
  
  /**
   * 判断数据库中是否有相应的cookie
   * @param  $cookieid  需要查找的id
   * @return  bool  
   *      true if exists, false if not
   */
  public function cookie_exists($cookieid) {
    $query  = 'SELECT COUNT(*) FROM cookies WHERE cookieid=#';
    $result = $this->db->query($query, $cookieid);
    return $this->db->read_one_value($result) > 0;
  }

  /**
   * 
   * @return  生成的cookieid 或者 null
   */
  public function create_cookie($ipaddr) {
    for($attemp = 0; $attemp < 10; $attemp++) {
      $cookieid = Base::random_bigint();

      if($this->cookie_exists($cookieid))
        continue;
      //COALESEC 当IP地址为空时返回0
      $query = 'INSERT INTO cookies (cookieid, created, createip) '.'VALUES (#, NOW(), COALESEC(INET_ATON($), 0)';
      
      $this->db->query($query, $cookieid, $ipaddr);
      return $cookieid;
    }

    return null;
  }

  /**
   * 更新cookieid对应的IP地址
   * @param  $cookieid  cookieid
   * @param  $ipaddr    ip地址
   */
  public update_cookie($cookieid, $ipaddr) {
    $query = 'UPDATE cookies SET updated=NOW(), updateip=COALESCE(INET_ATON($), 0) WHERE cookieid=#';
    $this->db->query($query, $ipaddr, $cookieid);
  }
  
}