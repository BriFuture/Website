<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime: 05.06
 * lastmodifytime: xx
 * filename: Cookie.class.php
 * 实现cookie有关的操作
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Cookie {
  const ID = 'ID';

  /**
   * 如果cookie中有相应的值就返回该值，否则返回空
   */
  public function get_cookieid() {
    return isset($_COOKIE[self::ID]) ? $_COOKIE[self::ID] :null;
  } 

  /**
   * 创建新cookie
   */
  public function create_cookie() {
    $cookieid = $this->get_cookieid();
    $dbCookie = new DbCookie();

    if(!isset($cookieid) || !$dbCookie->cookie_exists($cookieid))
      $cookieid = $dbCookie->create_cookie(Base::get_remote_ip());

    setcookie(self::ID, $cookieid, time()+86400*365, '/', COOKIE_DOMAIN);
    $_COOKIE[self::ID] = $cookieid;

    return $cookieid;
  }

  /**
   * 更新cookie
   * @param  $cookieid  客户端的cookie id
   * @param  $action    相应的操作
   */
  public function cookie_report_action($cookieid, $action) {
    $dbCookie = new DbCookie();
    $dbCookie->update_cookie($cookieid, Base::get_remote_ip());
  }
}