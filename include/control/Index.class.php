<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate:0508
 * filename: Index.class.php
 * view层的操作者，实现页面与后台的数据交互
 * 操作index页面
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Index extends Page{
  /**
   * 渲染index页面,default
   * @param  $msg  额外的信息 
   */
  public function view_index($msg=null) {
    // require_once CORE_PATH.'db/Db.class.php';
    // require_once CORE_PATH.'const.php';
    // $security = Security::getInstance();
    // $viewinfo = new Db_viewinfo();
    // $info=array(
    //   'ip' => $_SERVER['REMOTE_ADDR'],
    //   'sid' => $security->set_secure_value('sid'),
    //   'browser' => $_SERVER['HTTP_USER_AGENT'],
    // );
    // $viewinfo->view_add($info); 
    $this->view['title'] = 'sunmell的主页';
    $this->view['block'] = array(
      array(
        'href'        => Base::get_url_path("Info","Info"),
        'title'       => 'Some Info Test',
        'description' => 'Some Tested Info',
        'img'         => 'data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==', 
      ),
      array(
        'href'        => Base::get_url_path("TestJsGame"),
        'title'       => 'My Game In test',
        'description' => 'My JS Game In Test',
        'img'         => 'data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==',
      ),
      array(
        'href'        => '//blog.sunsmell.cc',
        'title'       => 'My Blog',
        'description' => 'my blog powered by wordpress',
        'img'         => 'data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==',
      ),
      array(
        'href'        => 'https://github.com/GitFuture',
        'title'       => 'My GitHub Account',
        'description' => 'almost all my codes are there',
        'img'         => '/static/img/githublogo.jpg',
      ),
    );
    $this->view['carousel-item'] = array(
      array(
        'src'         => '/static/img/sword_art_online_4.jpg', 
        'description' => 'sword art online', 
        'active'      => true,
        ),
      array(
        'src'         => '/static/img/sword_art_online_3.jpg', 
        'description' => 'sword art online', 
        ),
      array(
        'src'         => '/static/img/worst_one.jpg', 
        'description' => '落第骑士的英雄谭', 
        ),
      );
    $this->render();
  }

}