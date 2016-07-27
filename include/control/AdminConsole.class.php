<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate:07.25
 * filename: AdminConsole.class.php
 * 控制台，方便修改内容
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class AdminConsole extends Page {
  public function render() {
    $this->view['title'] = "console page";
    $this->get_options();
    
    $this->inc(__CLASS__);
  }

  private function get_options() {
    $dboptions = new DbOptions();
    $this->view['options'] = $dboptions->get_all_options();
    
    $dbimages = new DbImages();
    $this->view['images'] = $dbimages->get_all();

  }
}