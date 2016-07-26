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
  
  public function view_register($msg) {
    $this->inc(__CLASS__);
  }
}