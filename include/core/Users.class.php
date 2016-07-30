<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * 产生、获取随机码
 * modifydate:0729
 * filename: User.php
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

Class Users{
  // 定义用户等级常量
  const USER_LEVEL_BASIC   = 0;
  const USER_LEVEL_NORMAL  = 20;
  const USER_LEVEL_PRIMER  = 50;
  const USER_LEVEL_SPECIAL = 100;
  const USER_LEVEL_ADMIN   = 120;
  const USER_LEVEL_SUPER   = 150;
  //定义用户标志
  
  
  /**
   * 如果没有开启会话session，就打开它
   */
  public static function start_session() {
    //设置session的有效时间 
    @ini_set('session.gc_maxlifetime', 86400);
    // session 需要cookie
    @ini_set('session.use_trans_sid', false);
    @ini_set('session.cookie_domain', COOKIE_DOMAIN);
    //防止重复开启会话
    if(!isset($_SESSION))
      session_start();
  }

  /**
   * 判断用户是否登陆
   */
  public static function is_logged_in() {
    if(!is_null(self::get_logged_in_user_cache()))
      return true;
    return false;
  }

  /**
   * 缓存登陆用户的信息
   */
  public static function get_logged_in_user_cache() {
    $dbusers = new DbUsers();
    return $dbusers->select_info(array('column' => 'SID', 'handle' => session_id()));
  }
  /**
   * 获取用户信息  底层操作
   */
  public static function get_logged_in_user_field($field) {
    $user = self::get_logged_in_user_cache();
    return @$user[$field];
  }

  /**
   * @param  
   *    $handle  
   *    $info    需要的信息（score, level, name, email, avatar_path, flags）    
   */
  public static function get_user_info($handle, $info) {
    $dbusers = new DbUsers();
    $user = $this->view['users'] = $dbusers->select_info($handle);
    return @$user[$info];
  }

  public static function get_level_string($level) {

  }

  /**
   * 设置session前缀
   */
  public static function session_var_suffix() {

  }

  public static function session_verify_code() {

  }

  /**
   * 用用户名作为cookie
   */
  public static function set_session_cookie($handle, $sessioncode, $remember) {

  }

  /**
   * 清除cookie
   */
  public static function clear_session_cookie() {

  }

  /**
   * 用户登陆
   * @param  $handle  用户标识符
   *         $passwd  用户输入的密码
   * @return  1  登陆成功
   *          0  登陆失败
   *          2  已登录
   */
  public static function set_logged_in_user($handle, $passwd) {
    $dbusers = new DbUsers();
    $info = $dbusers->select_info(array('column' => 'name', 'handle' => $handle));
    if(self::check_passwd($passwd, $info['pass'], $info['passsalt'])) {
      var_dump($info['SID']);
      if(is_null($info['SID']) || $info['SID'] == '') {
        $dbusers->update(array('column' => 'name', 'handle' => $handle), array('SID' => session_id()));
        return 1;
      } else {
        return 2;
      }
    } else {
      return 0;
    }
  }

  /**
   * 登出
   */
  public static function logged_out() {
    if(self::is_logged_in()) {
      $dbusers = new DbUsers();
      return $dbusers->update(array('column' => 'name', 'handle' => self::get_logged_in_user_field('name')), array('SID' => ''));
    }
    return false;
  }

  /**
   * 判断是否是管理员
   */
  public static function is_manager() {
    if(Self::is_logged_in() && Self::get_logged_in_user_field('level') >= Self::USER_LEVEL_SUPER) {
      return true;
    }
    return false;
  }

  /**
   * 登陆方式
   */
  public static function get_logged_in_source() {

  }

  /**
   * 登陆用户的句柄
   */
  public static function get_user_handle() {

  }

  /**
   * userid和Handle互相转化
   */
  public static function userid_to_handle($uid) {

  }

  public static function handle_to_userid($handle) {

  }

  public static function user_permit_error($permit_option=null) {

  }

  public static function permit_error($permit_option, $userid, $level, $flags, $points=null) {

  }

  public static function permit_value_error($permit, $userid, $level, $flags) {
    
  }

  /**
   * @param  $passwd     密码原文
   *         $enc_passwd 加密后的字符串
   *         $passsalt   密码掺杂量
   * @return  bool
   */
  public static function check_passwd($passwd, $enc_passwd, $passsalt) {
    return (self::encrypt_passwd($passwd, $passsalt) == $enc_passwd) ? true : false;
  }

  /**
   * @param  $passwd  密码原文
   *         $passsalt  密码掺杂量
   */
  public static function encrypt_passwd($passwd, $passsalt) {
    $encrypt_code = crypt($passwd, $passsalt);
    return base64_encode($encrypt_code);
  }

}