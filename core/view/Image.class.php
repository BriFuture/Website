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
      require CORE_PATH.'const.php';
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
      case 'jpg':
      case 'png':
      case 'jpeg':
      case 'bmp':
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

}