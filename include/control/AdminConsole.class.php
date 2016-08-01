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
      Base::raw_redirect('/');
      return;
    }

    $this->view['title'] = "console page";
    $this->view['self-page'] = Base::get_url_path(__CLASS__);
    $this->view['security_image_key'] = Security::get_form_security_code("upload_image");
    $this->get_options();

    if(Base::super_post_text("upload_image")) {
      $this->upload_image();
    }

    $this->inc(__CLASS__);
    // $this->view['test'] = PatternDb::pattern_update_sql(array('columns' => array('img_name' => 'test_name', 'img_path' => 'test_path', 'number' => 1), 'where' => array(array('column' => 'img_id', 'op'=>'=','value' => '1'))), 'images', false);
  }

  private function get_options() {
    $dboptions = new DbOptions();
    $this->view['options'] = $dboptions->get_all_options();
    
    $dbimages = new DbImages();
    $this->view['images'] = $dbimages->get_all();
    // $this->view['test'] = Options::opt('index_carousel_pic_1');
    // $this->view['users'] = Users::get_logged_in_user_field('name');
  }

  private function upload_image() {
    $img['columns']['img_path'] = Base::super_post_text("img_path");
    if(is_numeric(stripos($_FILES['upload-image']['type'], 'image'))) {
      if ($_FILES["upload-image"]["error"] > 0) {
        $this->view['error']['upload-image'] = "Error: " . $_FILES["upload-image"]["error"] . "<br />";
      }
      else {
        $upload_path = WEB_ROOT.$img['columns']['img_path'];
        // echo "Upload: " . $_FILES["upload-image"]["name"] . "<br />";
        // echo "Type: " . $_FILES["upload-image"]["type"] . "<br />";
        // echo "Size: " . ($_FILES["upload-image"]["size"] / 1024) . " Kb<br />";
        // echo "Stored in: " . $_FILES["upload-image"]["tmp_name"];
        if(file_exists($upload_path)) {
          $this->view['error']['upload-image'] = "Image already exists （图片已存在）！";
        } else {
          move_uploaded_file($_FILES["upload-image"]["tmp_name"], $upload_path);
        }
      }
    } else {
      $this->view['error']['upload-image'] = "Invalid image file. File type is ".$_FILES['upload-image']['type'];
      return;
    }
    $img['columns']['img_name']    = Base::super_post_text("img_name");
    $img['columns']['img_group']   = Base::super_post_text("img_group");
    $img['columns']['description'] = Base::super_post_text("description");
    $img['columns']['addons']      = Base::super_post_text("addons");
    $dbimages = new DbImages();
    $result = $dbimages->create($img);
    if($result) {
      $this->view['success']['upload-image'] = "图片上传成功，数据导入成功";
    }
  }
}