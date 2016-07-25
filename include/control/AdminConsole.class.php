<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate:0508
 * filename: AdminConsole.class.php
 * 控制台，方便修改内容
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class AdminConsole extends Page {
  public function view_adminconsole() {
    $this->view['title'] = "console page";
    $this->get_options();
    $this->render();
  }

  private function get_options() {
    $dboptions = new DbOptions();
    $this->view['options'] = $dboptions->get_all_options();
  }
}