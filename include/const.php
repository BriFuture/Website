<?php 
/**
 * 定义常量
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}
//URL常量
$URLS = array(
  'logo'        => '/static/img/mylogo-light.png',#'index.php?action=utils&util=paint&p=static&n=mylogo-light.png',
  'body-header' => VIEW_PATH.'header.phtml',
  'body-footer' => VIEW_PATH.'footer-thank.phtml',
  'nothing-jpg' => WEB_ROOT.'static/img/nothing.jpg',
);
$TEXT = array(
  'mail_alter_body' => '由于某些未知的原因无法显示，请启用html显示该邮件。',
);
$GLOVARS = array(
  'sendmail' => 'random_codes',
);
$TEST = array(
  'avatar' => '/users/avatar/141841r3brd89v2x3vt8r5.jpg',
);