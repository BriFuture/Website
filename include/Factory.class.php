<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate:0430
 * filename: Factory.class.php
 * 工厂模式的生产工厂，便于管理
 * 
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Factory{

  function __construct() {

  }

  /**
   * 注册多个autoload
   */
  static function register() {
    // 注册多个autoload
    spl_autoload_register("self::autoload_control");
    spl_autoload_register("self::autoload_db");
    spl_autoload_register("self::autoload_view");
  }
  /**
   * @deprecated 直接使用autoload 引用加载
   */
  function my_require_once($class, $path=CONTROL_PATH) {
    require_once $path.$class.'.class.php';
  }

  /**
   * 自动加载类
   * @param $class 引用的类名
   * 
   */
  private static function autoload_control($class) {
    $php = CONTROL_PATH.$class.'.class.php';
    if(is_file($php))
      require_once $php;
  }

   /**
   * 自动加载类
   * @param $class 引用的类名
   * 
   */
  private static function autoload_db($class) {
    $php = DB_PATH.$class.'.class.php';
    if(is_file($php))
      require_once $php;
  }

  private static function autoload_view($class) {
    $php = VIEW_PATH.$class.'.class.php';
    if(is_file($php))
      require_once $php;
  }

  /**
   * @deprecated 
   * 当new 一个control_Users对象时 虽然能够引用php文件，
   * 但是类名却不正确，User.class.php中的类名为User而不是control_Users
   */
  static function autoload($class) {
    $php = CORE_PATH.strtr($class, '_', '/').'.class.php';
    require_once $php;
  }


  /**
   *  通过名字返回相应类的对象
   *  control中的类不必使用工厂，因为不用怎么更改,主要是对view层的类做工厂
   */
  public static function getObject($name) {
    switch ($name) {
      case 'Index':
        $object = new $name();
        break;
      default:
        $object = null;
        break;
    }
    return $object;
  }

  public static function getDbLimits() {
    return new DbLimits();
  }
}