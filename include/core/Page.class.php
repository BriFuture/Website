<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime: 05.02
 * lastmodifytime: 05.18
 * filename: Page.class.php
 * Control层和View层的接口
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

/**
 * 每一个子类都对应着一个页面或者图片
 */
abstract class Page {
  /**
   * 用于传递需要显示的数据
   */
  public $view = array();

  public function __construct() {
    Cookie::create_cookie(Base::get_remote_ip());
  }
  /**
   * 找到相应的包含html的文件并引用（渲染网页）
   * 如果没有指定名字，使用最后一个调用该函数的函数名
   * @param  $name  引用的文件名
   */
  /*public function render($name=null) {
    // $trace = debug_backtrace();
    // array_shift($trace); //移去栈顶的元素,返回被移除的元素
    // // var_dump($trace);
    // $name = strtolower($trace[0]['class']); //找到最后一个调用该函数的类的名称
    
    // Cookie::create_cookie();
    $this->set_reuse();
    $file = VIEW_PATH.$name.'.phtml';
    if (file_exists($file)) {
      include $file;
    }
    else {
      $err = new Err(array('errcode' => 404));
      $err->view_err();
    }
  }*/
  abstract public function render();


  protected function inc($name) {
    require INCLUDE_PATH.'const.php';
    $this->view['header'] = $URLS['body-header'];
    // $this->view['footer'] = $URLS['body-footer'];
    $this->view['footer'] = VIEW_PATH.'footer-thank.phtml';
    $this->view['logo']   = $URLS['logo'];

    $this->view['avatar'] = Users::get_logged_in_user_field('avatar');
    $this->view['login']  = 'login';
    $this->view['logout']  = 'logout';
    $this->view['reg']  = 'no-reg';
    include VIEW_PATH.strtolower($name).'.phtml';
  }


  /**
   * 
   * @return  返回格式化的信息
   */
  public static function format_msg($content, $title) {

  }
  
  /**
   * 内容分发函数
   * @param  $file  文件名
   * @param  $view  需要显示的逻辑
   */
  public static function dispatch($file='Index') {
    //首字母大写，将 Base::get_url() 重写之后不需要首字母大写了。
    // $classname = ucwords(str_replace('.php', '', strtolower($file)));
    $classname = str_replace('.php', '', $file);
    //如果存在相应的方法
    if(class_exists($classname))
    {
      $class  = new $classname();
      
      $class->render();
    }
    else
    {
      $err = new Err(array('errcode' => 404));
      $err->render();
    }
  }

}
