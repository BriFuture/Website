<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate:   04.30
 * lastmodify:  06.16
 * filename: Err.class.php
 * 有关err页面的操作，有关页面的逻辑都在页面类及其子类实现
 * 
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Err extends Page{
  private $msg;

  public function Err($msg=null) {
    $this->msg = $msg;
  }

  /**
   * 默认显示的err
   * @param  $msg  额外的信息
   */
  public function view_err() {
    //如果设置了errcode，直接显示对应的http码页面
    if(isset($this->msg['errcode']))
    {
      $this->view['errcode'] = $this->msg['errcode'];
    }
    elseif(isset($this->msg) && !isset($this->msg['errcode']))
    {
      $this->view['msg'] = $this->msg;
      $this->view['errcode'] = 0;
    }
    else
    {
      $this->view['errcode'] = Base::super_get('err');
    }

    if(isset($this->view['errcode']))
    {
      $this->err_header_ouptut();
    }
    //显示页面
    $this->render();
  }

  /**
   * 输出网页头部
   * @param  $errcode  错误码
   */
  function err_header_ouptut() {
    switch ($this->view['errcode']) {
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
        header("HTTP/1.0 404 Not Found");
        break;
    }
  }

  /**
   * 输出head标题, 封装了err_title_withnum()
   * @param  $errcode  错误码或者标题
   */
  function err_title_output() {
    // global $msg;
    if(!isset($this->msg) || isset($this->msg['errcode']))
    {
      //根据错误码输出页面head标题
       switch ($this->view['errcode']) {
        case '401':
          echo '没有权限访问';
          break;
        case '403':
          echo '没有权限访问';
          break;
        case '500':
          echo 'Internal Server Error';
          break;
        case '404':
          echo '页面找不到了';
          break;
        default:
          echo '未知错误';
      }
    }
    else
    {
      if(!isset($this->msg['title']) || is_null($this->msg['title']))
      {
        echo '未知的错误';
      }
      else
      {
        echo $this->msg['title'];
      }
    }
  }

}