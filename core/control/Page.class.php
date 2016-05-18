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
class Page {
  /**
   * 用于传递需要显示的数据
   */
  public $view = array();

  /**
   * 找到相应的包含html的文件并引用（渲染网页）
   * 如果没有指定名字，使用最后一个调用该函数的函数名
   * @param  $name  引用的文件名
   */
  public function render() {
    $trace = debug_backtrace();
    //移去栈顶的元素
    array_shift($trace);
    // var_dump($trace);
    //找到最后一个调用该函数的类的名称
    $name = strtolower($trace[0]['class']);

    $this->set_reuse();
    $file = PUBLIC_PATH.$name.'.phtml';
    // echo $file;
    if(file_exists($file))
    {
      include $file;
    }
    else
    {
      $this->err();
    }
  }

  public function set_reuse() {
    require CORE_PATH.'const.php';
    $this->view['header'] = $URLS['body-header'];
    $this->view['footer'] = $URLS['body-footer'];
    $this->view['logo']   = $URLS['logo'];
    $this->view['avatar'] = $TEST['avatar'];
  }
  /**
   * 由于dispath使用了该函数，所以并不直接将该函数去除
   */
  public function err($msg=null) {
    $err = new Err();
    $err->view_err($msg);
  }


  /**
   * 
   * @return  返回格式化的信息
   */
  public function format_msg($content, $title) {

  }
  
  /**
   * 内容分发函数
   * @param  $file  文件名
   * @param  $view  需要显示的逻辑
   */
  public function dispatch($file='Index', $view='index') {
    //首字母大写
    $classname = ucwords(str_replace('.php', '', $file));
    //如果存在相应的方法
    if(class_exists($classname))
    {
      $class  = new $classname();
      $method = 'view_'. $view;
      if(!method_exists($classname, $method))
      {
        $method = 'view_'.$classname;
      }
      $class->$method();
    }
    else
    {
      $this->err();
    }
  }

}
