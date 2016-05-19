<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * 产生、获取随机码
 * startdate: 04.2x
 * modifydate:05.01
 * filename: Security.class.php
 * 跨页面之后单例模式就失效了 注册模式失败
 * 用SESSION超全局变量代替
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}
class Security {
  // private $key_map = array();
  // private static $security;

  function __construct() {
  }
  
  /**
   *
   * @deprecated 放弃了，一旦页面跳转，无法实现在页面间共享对象
   */
  public static function getInstance() {
    unset($_SESSION['security']);
    if(isset($_SESSION['security'])) {
      self::$security = unserialize(base64_decode($_SESSION['security']));
      if(!(self::$security instanceof self))
        $_SESSION['security'] = base64_encode(serialize(new self));
      // print_r($_SESSION['security']);
    }
    else
    {
      $_SESSION['security'] = base64_encode(serialize(new self));
    }
    self::$security = unserialize(base64_decode($_SESSION['security']));
    // if(self::$security instanceof self)
    //   echo 'security is self';
    // else
    //   echo 'is not self';
    return self::$security;
  }

  /**
   * 产生长度为$length的随机码
   * @return random code
   */
  public function random($length) {
    $random = "";
    $random_codes=str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

    for($i = 0;$i < $length;$i++) {
      //产生随机码
      $random .= $random_codes[rand(0,count($random_codes) - 1)];
    }
    return $random;
  }
  /**
   * 创建key相关的键值对,
   * @param $key key的名称
   * @return 生成的随机码
  */
  public function set_secure_value($key, $length=64, $base64=false) {
    $random = random($length);

    if($base64)
      $random = base64_encode($random);
    // $this->key_map[$key] = $random;
    $_SESSION[$key] = $random;
    return $random;
    // echo count($key_map);
  }
  /**
   * 设置表单安全码
   * 设置COOKIE
   */
  public function set_form_security_key() {
    //引用外部变量
    global $form_key_cookie_set;

    $user = Factory::getUsers();

    if(!$user->is_logged_in() && !@$form_key_cookie_set) {
      $form_key_cookie_set = true;

      if(strlen(@$_COOKIE['secure_key']) != FROM_KEY_LENGTH) {
        //需要重新设置key
        $_COOKIE['secure_key'] = random(FROM_KEY_LENGTH);
      }

      setcookie('secure_key', $_COOKIE['secure_key'], time()+2*FORM_EXPIRY_TIME, '/', COOKIE_DOMAIN);
    }
  }

  /**
   * 计算表单的hash值
   * @return hash value
   */
  public function calc_form_security_hash($action, $timestamp) {
    //掺杂变量
    $salt = 'form_security_salt';

    require_once CORE_PATH.'Factory.class.php';
    $user = Factory::getUsers();

    if($user->is_logged_in())
      return sha1($salt.'/'.$action.'/'.$timestamp.'/'.$user->get_logged_in_userid().'/'.$user->get_logged_in_user_field('passsalt'));
    else
      return sha1($salt.'/'.$action.'/'.$timestamp.'/'.@$_COOKIE['secure_key']);
  }

  /**
   * 返回随机的安全码 
   * @return code
   */
  public function get_form_security_code($action) {
    self::set_form_security_key();

    $timestamp = ('db_time');

    $user = Factory::getUsers();
    return (int)($user->is_logged_in()).'-'.$timestamp.'-'.self::calc_form_security_hash($action, $timestamp);
  }

  /**
   * 验证安全码
   * @return bool
   */
  public function check_form_security_code($action, $value) {
    require_once CORE_PATH.'Factory.class.php';
    $users = Factory::getUsers();

    //需要报告的错误
    $report_problems = array();
    //一般错误
    $silent_problems = array();

    if(!isset($value)) 
      $silent_problems[] = 'code 丢失';
    elseif(!strlen($value))
      $silent_problems[] = 'code 为空';
    else
    {
      //以-为分隔符分离$value
      $parts = explode('-', $value);

      //$parts为3个部分
      if(count($parts) == 3) 
      {
        $logged_in = $parts[0];
        $timestamp = $parts[1];
        $hash      = $parts[2];
        $timenow   = 'now_time';

        if($timestamp > $timenow)
          //表单时间比当前时间大，表单有问题
          $report_problems[] = 'time '.($timestamp-$timenow).'s in future';
        elseif($timestamp < ($timenow-FPRM_EXPIRY_SECS))
          //尚未超时
          $silent_problems[] = '在 '.($timenow-$timestamp).'s 后超时';

        if($users->is_logged_in())
        {
          if(!$logged_in)
            $silent_problems[] = '正在登陆';
        }
        else
        {
          if($logged_in)
            $silent_problems[] = '正在退出';
          else
          {
            //取出COOKIE中的key
            $key = @$_COOKIE['secure_key'];

            if(!isset($key))
              $silent_problems[] = '缺少 COOKIE KEY ';
            elseif(!strlen($key))
              $silent_problems[] = 'COOKIE KEY 为空 ';
            elseif(strlen($key) != FORM_KEY_LENGTH)
              $report_problems[] = 'COOKIE KEY ('.$key.') 无效 ';
          }
        }

        //如果没有问题产生
        if(empty($silent_problems) && empty($report_problems))
          if(strtolower(self::calc_form_security_hash($action, $timestamp)) != strtolower($hash))
            //但是随机码不匹配
            $report_problems[] = 'code 不匹配';
      }
      else
        //不是3个部分就有错误
        $report_problems[] = 'code '.$value.' 错误';
    }
    //记录错误
    if(count($report_problems))
      @error_log('PHP SunSmell form security violation for '.$action.
      ' by '.($users->is_logged_in ? ('userid '.$users->get_logged_in_userid()) : 'anonymous').
      ' ('.implode(',', array_merge($report_problems, $silent_problems)).')'.
      ' on '.@$_SERVER['REQUEST_URI'].
      ' via '.@$_SERVER['HTTP_REFERER']
      );

    return (empty($silent_problems) && empty($report_problems));

  }

  /**
   *
   * 获取key中存储的值  
   * @return null or the code
  */
  public function get_secure_value($key, $base64=false) {
    // if(array_key_exists($key, $this->key_map)) 
    if(isset($_SESSION[$key]))
    {
      // $value = $this->key_map[$key];
      $value = $_SESSION[$key];
      if($base64)
        $value = base64_decode($value);
      return $value;
    } 
    // echo '<br>key: '.$key;
    // foreach ($this->key_map as $keys => $value) {
    //   echo '<br>keymapkeys: '.$keys;
    // }
    return null;
  }

  /**
   * 检测验证码是否正确
   * @param $key key的名称
   * @param $random_codes 输入的随机码
   * @return bool
  */
  public function check_code($key, $random_codes, $base64=false) {
    $code = $this->get_secure_value($key,$base64);
    // echo '<br>code:'.$code;
    // echo '<br>randomcode:'.$random_codes;
    if(!is_null($code) && $code == $random_codes)
      return true;
    // echo 'false in check ';
    return false;
  }
}