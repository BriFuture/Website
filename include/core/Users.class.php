<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * 产生、获取随机码
 * modifydate:0429
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
  public function start_session() {
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
  public function is_logged_in() {

  } 
  /**
   * 缓存登陆用户的信息
   */
  public function get_logged_in_user_cache() {

  }
  /**
   * 获取用户信息  底层操作
   */
  public function get_logged_in_user_field($field) {
    $user = self::get_logged_in_user_cache();
    return @$user[$field];
  }
  /**
   * 获取用户UID
   */
  public function get_logged_in_user_id() {
    return self::get_logged_in_user_field('uid');
  }

  /**
   * 生成指定格式的用户UID
   */
  public function get_format_uid() {

  }

  /**
   * 获取登陆用户的分数
   */
  public function get_user_score($handle) {

  }

  /**
   * 等级
   */
  public function get_user_level($handle) {

  }
  public function get_level_string($level) {

  }
  /**
   *
   */
  public function get_user_name($handle) {

  }

  /**
   * 邮箱
   */
  public function get_user_email($handle) {

  }

  /**
   * 设置session前缀
   */
  public function session_var_suffix() {

  }

  public function session_verify_code() {

  }

  /**
   * 用用户名作为cookie
   */
  public function set_session_cookie($handle, $sessioncode, $remember) {

  }

  /**
   * 清除cookie
   */
  public function clear_session_cookie() {

  }

  public function set_session_user() {

  }

  public function clear_session_user() {

  }

  /**
   * 用户登陆
   */
  public function set_logged_in_user() {

  }

  /**
   * 获取登陆用户
   */
  public function get_logged_in_user() {

  }

  /**
   * 登陆方式
   */
  public function get_logged_in_source() {

  }

  /**
   * 个人页面
   * 感觉没必要做
   */
  public function get_one_user_html($handle) {

  }

  /**
   * 头像路径
   */
  public function get_user_avatar_path($handle) {

  }

  /**
   * 登陆用户的句柄
   */
  public function get_user_handle() {

  }

  /**
   * 用户标志
   */
  public function get_user_flags() {

  }

  /**
   * userid和Handle互相转化
   */
  public function userid_to_handle($uid) {

  }

  public function handle_to_userid($handle) {

  }

  public function user_permit_error($permit_option=null) {

  }

  public function permit_error($permit_option, $userid, $level, $flags, $points=null) {

  }

  public function permit_value_error($permit, $userid, $level, $flags) {
    
  }
}