<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime:       07.30
 * filename:    Ajax.class.php
 * description: 管理分配 Ajax 请求
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Ajax {
  /*
  private static function ajax_file_exists($file) {
    return file_exists(AJAX_PATH.$file.'.php');
  }*/

  public static function request($req) {
    // if(self::ajax_file_exists($file)) {
    //   include AJAX_PATH.$file.'.php';
    // } else {
    //   return null;
    // }
    if(method_exists('Ajax', $req)) {
      self::$req();
    }
  }

  public static function ip() {
    echo $_SERVER['REMOTE_ADDR'];
  }

  public static function add() {
    $num1 = (int) Base::super_post_text('num1');
    $num2 = (int) Base::super_post_text('num2');
    // var_dump( $num1);

    echo $num1 + $num2;
  }

  public static function alter_image() {
    $action = Base::super_post_text('action');
    $img['columns']['img_name']    = Base::super_post_text("img_name");
    $img['columns']['img_path']    = Base::super_post_text("img_path");
    $img['columns']['img_group']   = Base::super_post_text("img_group");
    $img['columns']['description'] = Base::super_post_text("description");
    $img['columns']['addons']      = Base::super_post_text("addons");
    $dbimages = new DbImages();
    switch ($action) {
      case 'modify':
        $img_id = (int) Base::super_post_text("id");
        // echo "{".$img_id.', '.$img_name.', '.$img_path.', '.$img_group.', '.$description.', '.$addons.'}';
        $img['where'] = array(array('column' => 'img_id', 'op'=> '=', 'value' => $img_id));
        $status = $dbimages->update($img);
        echo json_encode(array('status' => $status));
        break;
      case 'delete':
        $img_id = (int) Base::super_post_text("id");
        $status = $dbimages->delete($img_id);
        echo json_encode(array('status' => $status));
        break;
      /*case 'add':
        if(strlen($img['columns']['img_path']) == 0 || strlen($img['columns']['img_group']) == 0) {
          echo json_encode(array('status' => 0));
        } else {
          // $dbimages->create($img);
          // echo 'add:{, '.$img_name.', '.$img_path.', '.$img_group.', '.$description.', '.$addons.'}';
          
          echo json_encode(array('status' => 1));
        }
        break;
      case 'upload':
        if ($_FILES["upload-image"]["error"] > 0) {
          echo "Error: " . $_FILES["upload-image"]["error"] . "<br />";
        }
        else {
          echo "Upload: " . $_FILES["upload-image"]["name"] . "<br />";
          echo "Type: " . $_FILES["upload-image"]["type"] . "<br />";
          echo "Size: " . ($_FILES["upload-image"]["size"] / 1024) . " Kb<br />";
          echo "Stored in: " . $_FILES["upload-image"]["tmp_name"];
        }
        break;*/
    }
  }
}