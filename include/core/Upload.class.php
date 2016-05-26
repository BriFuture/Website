<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime: 05.06
 * lastmodifytime: 05.xx
 * filename: Upload.class.php
 * 
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Upload() {

  /**
   * 最大上传文件大小
   * @return  允许的最大值,以字节为单位
   */
  public function get_max_upload_size() {
    $mindb = 16777215;  // MEDIUMBLOB column type

    //从php.ini中读取上传的最大值
    $minphp = trim(ini_get('upload_max_filesize'));

    switch (strtolower(substr($minphp, -1))) {
      case 'g':
        $minphp *= 1024;
      case 'm':
        $minphp *= 1024;
      case 'k':
        $minphp *= 1024;
        break;
    }

    return min($mindb, $minphp);
  }

  /**
   *
   */
  public function upload_file($local_filename, $source_filename, $max_filesize=null, $only_image=false, $image_maxwidth=null, $image_maxheight=null) {

  }

  /**
   * 上传单个文件时
   * @return  bool
   */
  public function upload_one_file($max_filesize=null, $only_image=false, $image_maxwidth=null, $image_maxheight=null) {
    $file = reset($_FILES);

    return upload_file($file['tmp_name'], $file['name'], $max_filesize, $only_image, $image_maxwidth, $image_maxheight);
  }
}