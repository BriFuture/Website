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

  public function render() {
    $this->view['titile'] = "Login";
    $this->inc(__CLASS__);
  }
}