<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate:0521
 * filename: Info.class.php
 * 有关Info页面的操作
 * 
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Info extends Page{
  public function view_info($msg=null) {
    $this->render();
  }
}