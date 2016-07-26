<?php
/**
 * 网站入口
 * 单入口模式
 * author: future <zhoujw@sunsmell.cc>
 * last-modify: 0518
 */
//定义版本
define('VERSION', '1.0.5');
//定义路径常量
define('WEB_ROOT',     __DIR__."/"            );
define('INCLUDE_PATH', WEB_ROOT.'include/'    );
define('CORE_PATH',    INCLUDE_PATH."core/"   );
define('CONTROL_PATH', INCLUDE_PATH.'control/');
define('DB_PATH',      INCLUDE_PATH.'db/'     );
define('VIEW_PATH',    INCLUDE_PATH.'view/'   );
define('AJAX_PATH',    INCLUDE_PATH.'ajax/'   );
define('UTIL_PATH',    INCLUDE_PATH.'utils/'  );

//初始化配置
require_once INCLUDE_PATH.'config.php';
require_once INCLUDE_PATH.'Factory.class.php';
//注册函数
Factory::register();
//初始化配置php
Base::initialize_php();
//打开会话
$users = new Users();
$users->start_session();
//分发
Base::dispatch();