<?php 
/** 
 * @author future <zhoujw@sunsmell.cc>
 * startdate:0528
 * filename: Register.class.php
 * 实现有关注册的操作
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Register extends Page {
  private $email;
  private $username;
  private $passwd;
  private $code;
  private $contact;
  private $picture;
  private $passsalt;


  private $error;
  
  public function render() {
    if(!Base::super_post_text("code")) {
      $this->default_dis();
    } else {
      $this->email = Base::super_post_text("email");
      $this->username = Base::super_post_text("username");
      $this->passwd = Base::super_post_text("passwd");
      $this->code = Base::super_post_text("code");
      $this->validate_input();
    }
  }

  /**
   * 默认的显示页面
   */
  private function default_dis() {
    Security::set_form_security_key();
    $this->view['secure_code'] = Security::get_form_security_code("register");
    $this->view['title'] = "Register";
    $this->inc(__CLASS__);
  }

  /**
   * 检查输入
   */
  private function validate_input() {
    if(!preg_match('/^\w+[\w\.]*@[\w]+(\.[\w]+)+/', $this->email)) {
      $this->error['email'] = '邮箱 '.$this->email.' 有误。';
    } else {
      // echo '邮箱 '.$this->email.' 无误。';
    }
    //没有错误就跳转到主页
    if(count($this->error) == 0) {
      $dbusers = new DbUsers();
      // echo '邮箱 '.$this->email;
      $this->passsalt = $dbusers->get_passsalt();

      $newuser = array(
        'name'    => $this->username,
        'passwd'    => $this->encrypt_passwd($this->passsalt),
        'email'   => $this->email,
        'level'   => 100,
        'contact' => '',
        'picture' => '',
        'score'   => 0,
        'passsalt' => $this->passsalt,
      );
      $dbusers->add_user($newuser);
      print_r($dbusers->select_info($))
    } else {
      $this->default_dis();
    }
  }

  /**
   * 用不可逆的算法加密
   */
  private function encrypt_passwd($passsalt) {
    $encrypt_code = crypt($this->passwd, $passsalt);
    return base64_encode($encrypt_code);
  }

}