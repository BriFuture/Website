<?php
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}

//==================//
define('DEBUG_MODE', true);
define('ALLOW_MAIL', false);
//表单随机码长度
define('FORM_KEY_LENGTH', 64);
//表单超时时间设为6小时
define('FORM_EXPIRY_TIME', 60*60*6);
define('COOKIE_DOMAIN','');
define('URL_FORMAT', 0);
define('FORM_EXPIRY_SECS', 600);