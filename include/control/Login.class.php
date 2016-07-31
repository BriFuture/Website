<?php 
/** 
 * @author future <zhoujw@sunsmell.cc>
 * startdate:0528
 * filename: Login.class.php
 * 实现有关登陆的操作
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Login extends Page {
  private $handle;
  private $passwd;
  private $code;
  private $remember;
  private $to;

  public function render() {
    if(Users::is_logged_in()) {
      Users::logged_out();
      Base::raw_redirect(Base::super_get('to'));
    } else if(is_null(Base::super_post_text('code'))) {
      $this->dis();
    } else {
      $this->handle   = Base::super_post_text('handle');
      $this->passwd   = Base::super_post_text('passwd');
      $this->code     = Base::super_post_text('code');
      $this->remember = Base::super_post_text('remember');
      $this->to       = Base::super_post_text('to');
      $this->logon();
    }

  }

  public function dis() {
    Security::set_form_security_key();
    $this->view['secure_code'] = Security::get_form_security_code("login");
    $this->view['title'] = "Login";
    $this->inc(__CLASS__);
  }

  public function logon($fromreg=false) {
    if(!$fromreg) {
      if(!Security::check_form_security_code('login', $this->code)) {
        $this->view['error'] = '登陆有误';
      } else {
        // $dbusers = new DbUsers();
        $logged = Users::set_logged_in_user($this->handle, $this->passwd);
        switch ($logged) {
          case 0:
            $this->view['error'] = 'Login Failed, Something wrong with Username or Passward（登陆失败。用户名或密码错误）！';
            break;
          case 1:
            if($this->remember) {

            }
            // echo 'logged in';
            Base::raw_redirect(($this->to) ? $this->to : '/');
            return;
            break;
          case 2:
            $this->view['error'] = 'Already logged in（同名用户已在线）！';
            break;
          default:
            $this->view['error'] = 'Unkown error（未知的错误）！';
            break;
        }
        $this->dis();
      }
    } else {
      $args = func_get_args();
      // $args = array_slice($args, 1);
      Users::set_logged_in_user($args[1],$args[2]);
    }
  }

  public function set_remember() {
    
  }

}