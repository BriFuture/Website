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
    if(!Users::is_manager()) {
      return;
    }
    $this->view['title'] = "console page";
    $this->get_options();
    
    $this->view['test'] = PatternDb::pattern_update_sql(array('columns' => array('img_name' => 'test_name', 'img_path' => 'test_path', 'number' => 1), 'where' => array(array('column' => 'img_id', 'op'=>'=','value' => '1'))), 'images', false);
    $this->inc(__CLASS__);
  }

  private function get_options() {
    $dboptions = new DbOptions();
    $this->view['options'] = $dboptions->get_all_options();
    
    $dbimages = new DbImages();
    $this->view['images'] = $dbimages->get_all();
    $this->view['users'] = Users::get_logged_in_user_field('name');
  }
}