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
  public function render($msg=null) {
    
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

    $dbimages = new DbImages();
    $this->view['carousel-item'] = $dbimages->get_images_by_group("index_page");

    $this->inc(__CLASS__);
  }

}