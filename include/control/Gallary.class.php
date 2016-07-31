<?php 
/** 
 * @author future <zhoujw@sunsmell.cc>
 * startdate:0731
 * filename: Gallary.class.php
 * 展示图片
 */

if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Gallary extends Page {
  public function render() {

    $this->inc(__CLASS__);
  }
}