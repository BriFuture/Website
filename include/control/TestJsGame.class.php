<?php 
/** 
 * @author future <zhoujw@sunsmell.cc>
 * startdate:0528
 * filename: TestJsGame.class.php
 * 测试前端 js
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class TestJsGame extends Page{
  public function view_testjsgame() {
    $this->view['title'] = "Test JS Game";
    $this->render();
  }
}