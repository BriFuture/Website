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
  public static function get_cookieid() {
    return isset($_COOKIE[self::ID]) ? $_COOKIE[self::ID] :null;
  } 

  /**
   * 创建新cookie
   */
  public static function create_cookie() {
    $cookieid = self::get_cookieid();
    $dbCookies = new DbCookies();

    if(!isset($cookieid) || !$dbCookies->cookie_exists($cookieid))
      $cookieid = $dbCookies->create_cookie(Base::get_remote_ip());

    setcookie(self::ID, $cookieid, time()+86400*365, '/', COOKIE_DOMAIN);
    $_COOKIE[self::ID] = $cookieid;

    return $cookieid;
  }

  /**
   * 更新cookie
   * @param  $cookieid  客户端的cookie id
   * @param  $action    相应的操作
   */
  public static function cookie_report_action($cookieid, $action) {
    $dbCookies = new DbCookies();
    $dbCookies->update_cookie($cookieid, Base::get_remote_ip());
  }
}