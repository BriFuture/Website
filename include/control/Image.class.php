<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime: 05.08
 * lastmodifytime: 05.xx
 * filename: Image.class.php
 * Control层和View层的接口，但不render网页，主要用作绘制图片等等
 * 生成图片的话
 * png图片需要保留alpha通道
 * gif图片如果是动态图需要能变化，目前还没实现
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Image {

  public function paint() {

  }

  /**
   * 输出内容
   * @param  $width  图片宽度  default 100
   * @param  $height 图片高度  default 100
   */
  function output($width=100, $height=100) {
    $path = Base::super_get('p');
    // echo 'path:'.$path;
    $name = Base::super_get('n');
    $addition = Base::super_get('a');
    // $type = @$_GET['t'];
    if(is_null($path) || is_null($name))
      return;
    //根据URL的path选择
    if(isset($path))
    {
      switch ($path) {
        case 'static':
          $path = WEB_ROOT.'static/'.(strlen($addition) ? $addition.'/' : 'img/');
          // echo $path;
          break;
        case 'dynamic':
          $path = WEB_ROOT.'users/img/';
          break;
      }
    }
    //获取图片类型
    $type = substr($name, stripos($name, '.')+1);

    $im = $this->create_picture($type, $path.$name);
    if(!$im)
    {
      //无法读取图片时
      require INCLUDE_PATH.'const.php';
      $im = imagecreatefromjpeg($URLS['nothing-jpg']);
      $this->imagepicture('jpg',$im);
    }
    else
    {
      // $im2 = @imagecreate($width, $height);
      // $background_color = imagecolorallocate($im, 255, 255, 255);
      // $text_color = imagecolorallocate($im, 255, 0, 0);
      // $string="ERROR";
      // imagestring($im, 5, $width/5, $height/3, $string, $text_color);
      //set the enviroment variable for GD
      // putenv('GDFONTPATH='.realpath('.'));
      // $font="msyhbd";
      // imagefttext($im, 48, 0, $width/5, $height/3, $text_color, $font, $string);
      imagepicture($type,$im);
    }
  }

  /**
   * 发送头部消息
   * @param  $type  图片文件的格式
   */
  function choose_header($type) {
    $content = "Content-type: image/";
    switch ($type) {
      case IMAGETYPE_GIF:
      case IMAGETYPE_PNG:
      case IMAGETYPE_JPEG:
        $content.=$type;
        break;
      default:
        $content.='gif';
        break;
    }
    header($content);
  }

  /**
   * 读取图片内容
   * @param  $type  图片的格式
   * @param  $name  文件名
   * @return  图片文件句柄
   */
  function create_picture($type, $name) {
    switch ($type) {
      case 'jpg':
      case 'jpeg':
        $im = @imagecreatefromjpeg($name);
        break;
      case 'png':
        $im = @imagecreatefrompng($name);
        //用来保存通道信息，如果没有的话，图片会变得很奇怪
        imagesavealpha($im,true);
        break;
      case 'gif':
        $im = @imagecreatefromgif($name);
        break;
    }
    return $im;
  }

  /**
   * 画出图片
   * @param  $type  图片格式
   * @param  $im    图片句柄
   */
  function imagepicture($type,$im) {
    $this->choose_header($type);
    switch ($type) {
      case 'jpg':
      case 'jpeg':
        imagejpeg($im);
        break;
      case 'png':

        imagepng($im);
        break;
      case 'gif':
        imagegif($im);
        break;
    }
    //释放资源
    imagedestroy($im);
  }

  /**
   * 判断php是否支持gd绘图
   * @return  bool
   */
  public function has_gd_image() {
    return extension_loaded('gd') && function_exists('imagecreatefromstring') && function_exists('imagejpeg');
  }

  /**
   * 判断图片文件是否太大
   * @param  $imagefile  图片文件
   * @param  $size  设置的大小
   * @return 返回计算出的数据字节大小和所需要字节大小的1.5倍的平方
   */
  public function image_file_too_big($imagefile, $size=null) {
    if(function_exists('memory_get_usage'))
    {
      $got_bytes = trim(@ini_get('memory_limit'));

      switch (strtolower(substr($got_bytes, -1))) {
        case 'g':
          $got_bytes *= 1024;
        case 'm':
          $got_bytes *= 1024;
        case 'k':
          $got_bytes *= 1024;
      }

      if($got_bytes > 0) {
        //safety margin of 10%
        $got_bytes = ($got_bytes - memory_get_usage()) * 0.9;

        $need_bytes = filesize($imagefile); //memory to store file contents

        $image_size = @getimagesize($imagefile);

        if(is_array($image_size)) //parse image error
        {
          $width    = $image_size[0];
          $height   = $image_size[1];
          $bits     = isset($image_size['bits']) ? $image_size['bits'] : 8;
          $channels = isset($image_size['channels']) ? $image_size['channels'] : 3;

          $need_bytes += $width * $height * $bits * $channels / 8 * 2;  //bytes to load original image

          if(isset($size) && $this->image_constrain($width, $height, $size))
          {
            $need_bytes += $width * $height * 3 * 2;
          }
        }

        if($need_bytes > $got_bytes)
        {
          return sqrt($got_bytes / ($need_bytes * 1.5));  //JPEG quality may change
        }
      }
    }

    return false;
  }

  /**
   * 图片数据限制
   * @param  &$width     宽度限制
   * @param  &$height    长度限制
   * @param  $max_width  最大的宽度
   * @param  $max_height 最大宽度，如果未指定，就用max_width
   * @return  返回图片数据
   */
  public function image_constrain_data($image_data, &$width, &$height, $max_width, $max_height=null) {
    $inimage = @imagecreatefromstring($image_data);

    if(is_resource($inimage)) {
      $width  = imagesx($inimage);
      $height = imagesy($inimage);

      $this->image_constrain($width, $height, $max_width, $max_height);
      $this->gd_image_resize($inimage, $width, $height);
    }

    if(is_resource($inimage)) {
      $image_data = $this->gd_image_jpeg($inimage);
      imagedestroy($inimage);
      return $imagedata;
    }

    return null;
  }

  /**
   * 判断图片是否超过限制，如果超过限制的话按照比例缩小
   * @param  &$width     宽度限制
   * @param  &$height    长度限制
   * @param  $max_width  最大的宽度
   * @param  $max_height 最大宽度，如果未指定，就用max_width
   * @return  bool  
   *       true  超过限制  false  没有超过
   */
  public function image_constrain(&$width, &$height, $max_width, $max_height=null) {
    if(!isset($max_height))
    {
      $max_height = $max_width;
    }

    if($width > $max_width || $height > $max_height)
    {
      $multiplier = min($max_width/$width, $max_height / $height);  //the minimum ratio 
      $width      = floor($width * $multiplier); 
      $height     = floor($height * $multiplier);

      return true;
    }

    return false;
  }

  /**
   * 改变图片大小
   * @param  &$image  图片的指针
   * @param  $width   图片的宽度
   * @param  $height  图片的高度
   */
  public function gd_image_resize(&$image, $width, $height) {
    //save image 
    $old_image = $image;
    $image     = null;

    $new_image = imagecreatetruecolor($width, $height);
    $white     = imagecolorallocate($new_image, 255, 255, 255);  //white background
    imagefill($new_image, 0, 0, $white);

    if(is_resource($new_image)) {
      if(imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $width, $height, imagesx($old_image), imagesy($old_image)))
      {
        $image = $new_image;
      }
      else
      {
        imagedestroy($new_image);
      }
    }

    imagedestroy($old_image);
  }

  /**
   * 绘出jpeg格式的图片
   * @param  $image  图片句柄
   * @return  
   */
  public function gd_image_jpeg($image, $output=false) {
    ob_start();
    imagejpeg($image, null, 90);
    return $output ? ob_get_flush() : ob_get_clean();
  }

  /**
   * 返回gd支持的图片格式
   */
  public function gd_image_formats() {
    $image_type_bits = imagetypes();

    $bit_strings = array(
      IMG_GIF => 'GIF',
      IMG_JPG => 'JPG',
      IMG_PNG => 'PNG',
    );

    foreach(array_keys($bit_strings) as $bit) {
      $if(!($image_type_bits & $bit))
      {
        unset($bit_strings[$bit]);
      }
    }

    return $bit_strings;
  }


}