<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate:0430
 * filename: Err.class.php
 * 有关err页面的操作，有关页面的逻辑都在页面类及其子类实现
 * 
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Err extends Page{
  // public $view = array();
  // private $msg;

  /**
   * 默认显示的err
   * @param  $msg  额外的信息
   */
  public function view_err($msg=null) {
    //如果设置了errcode，直接显示对应的http码页面
    if(isset($msg['errcode']))
    {
      $this->view['errcode'] = $msg['errcode'];
    }
    elseif(isset($msg) && !isset($msg['errcode']))
    {
      $this->view['msg'] = $msg;
      $this->view['errcode'] = 0;
    }
    else
    {
      $this->view['errcode'] = Base::super_get('err');
    }

    //引用常量
    // require CORE_PATH.'const.php';

    if(isset($this->view['errcode']))
      $this->err_header_ouptut($this->view['errcode']);
    $this->render();
  }

  /**
   * 输出网页头部
   * @param  $errcode  错误码
   */
  function err_header_ouptut($errcode) {
    switch ($errcode) {
      case '401':
        header("HTTP/1.0 401 Unauthorized");
        break;
      case '403':
        header("HTTP/1.0 403 Forbidden");
        break;
      case '500':
        header("HTTP/1.0 500 Internal Server Error");
        break;
      case '404':
      default:
        header("HTTP/1.0 404 Not Found");
        break;
    }
  }

  /**
   * 输出head标题, 封装了err_title_withnum()
   * @param  $errcode  错误码或者标题
   */
  function err_title_output($errcode) {
    global $msg;
    if(!isset($msg) || isset($msg['errcode']))
    {
      $this->err_title_withnum($errcode);
    }
    else
      if(!isset($msg['title']) || is_null($msg['title']))
        echo '未知的错误';
      else
        echo $msg['title'];
  }

  /**
   * 根据错误码输出页面head标题
   * @param  $errcode  错误码 
   */
  function err_title_withnum($errcode) {
    switch ($errcode) 
    {
      case '401':
        echo '401 error 没有权限访问';
        break;
      case '403':
        echo '403 error 没有权限访问';
        break;
      case '500':
        echo '500 Internal Server Error';
        break;
      case '404':
      default:
        echo '404 页面找不到了';
        break;
    }
  }
}