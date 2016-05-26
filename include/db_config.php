<?php 
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

//optional setting
//调试性能
// defined('DEBUG_PERFORMANCE') || 
  define('DEBUG_PERFORMANCE', false);
//允许连接数据库，方便维护数据库
// defined('ALLOW_CONNECT_DB') || 
  define('ALLOW_CONNECT_DB', true);

/**
 * 定义数据库参数
 */
//测试本地
defined('HOST')||define('HOST','127.0.0.1');
defined('DATABASE')||define('DATABASE', 'mydata');
defined('USER')||define('USER', 'root');
defined('PASSWD')||define('PASSWD', '');