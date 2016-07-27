<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate:07.26
 * filename: Blog.class.php
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Blog extends Page {
  public function render() {
    $this->view['title'] = "blog page";    
    $this->inc(__CLASS__);
  }

}