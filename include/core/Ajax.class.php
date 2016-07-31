<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime:       07.30
 * filename:    Ajax.class.php
 * description: 管理分配 Ajax 请求
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Ajax {
  
  private static function ajax_file_exists($file) {
    return file_exists(AJAX_PATH.$file.'.php');
  }

  public static function request($file, $args=null) {
    if(self::ajax_file_exists($file)) {
      include AJAX_PATH.$file.'.php';
    } else {
      return null;
    }
  }
}