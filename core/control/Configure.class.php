<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime: 05.03
 * lastmodifytime: 05.04 over
 * filename: Configure.class.php
 * 管理配置文件
 * 增添，读取，更改或删除配置
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Configure {
  const DEFAULT_CONFIG = 'config.php';
  /**
   *  保持数组
   */
  private $start_array;
  /**
   *  配置
   */
  private $config_array;
  /**
   *  配置文件
   */
  private $config_file;
  /**
   * 读取配置文件
   * @param  $config_file  配置文件的路径
   * 
   */
  public function __construct($config_file=self::DEFAULT_CONFIG) {
    $this->config_file = CORE_PATH.$config_file;

    if(!file_exists($this->config_file))
    {
      $base = Factory::getBase();
      $base->fatal_error(500);
      // $base->fatal_error($this->config_file, 'config file not found');
    }
    else
    {
      //读写方式打开
      // $file = fopen($this->config_file, 'r+');
      $contents = file_get_contents($this->config_file);

      $file_array = explode("\n", $contents);
      // echo count($file_array);
      print_r($file_array);
      // htmlspecialchars(nl2br($file_array[0]));

      $start_line=0;
      foreach ($file_array as $key => $value) {
        //找到开始标志
        if(preg_match('/\/{2}=*\/{2}/', $value))
          $start_line = $key;
      }
      //起始行，修改的话只用修改一部分，其它的保持原样
      $this->start_array = array_slice($file_array, 0, $start_line + 1);
      // print_r($this->start_array);
      //得到config_array
      $this->config_array = array_slice($file_array, $start_line + 1);

      // print_r(array_merge($this->start_array, $this->config_array));
      // print_r($this->config_array);
      //截取字符串
      // $contents = substr($contents, stripos($contents, $match[0])+strlen($match[0]) );
      //转换成数组
      // $content_array = explode("\n", htmlspecialchars($contents));
    }
  }

  /**
   * 找到相应的行并返回行号，起始的行号为第一个define所在的行，记为0
   * @return 行号
   *   当返回值为-1时，表示没有找到，否则返回一个非负值
   */
  private function pattern($name) {
    $row = -1;

    foreach($this->config_array as $key => $value) {
      // echo $value.'<br>';
      if(stripos($value, $name)) 
        $row = $key;
    }
    return $row;
  }

  /**
   *  将修改后的内容写入到文件中
   */
  private function write() {
    if(count($this->start_array) && count($this->config_array)) {
      //将数组合并
      $array = array_merge($this->start_array, $this->config_array);
      //implode成为字符串
      $str = implode("\n", $array);
      // print_r(nl2br(htmlspecialchars($str)));
      // fopen($this->config_file, 'r+');
      file_put_contents($this->config_file, $str);
    }
  }

  /**
   * 添加一项
   * @deprecated 在实际中不需要添加配置，可能添加了配置并没有用
   * @param  $name  名称
   * @param  $value  值
   * @return  bool
   *      true  操作成功    false  操作失败（未知的错误）
   */
  public function add_config($name, $value) {
    return false;
  }

  /**
   * 删除一项
   * @deprecated 删除配置可能导致程序出错
   * @param  $name  名称
   * @return  bool
   *      true  操作成功    false  操作失败（未知的错误）
   */
  public function delete_config($name) {
    return false;
  }

  /**
   * 修改一项
   * @param  $name  名称
   * @param  $new_value  新值
   * @return  bool
   *      true  操作成功    false  操作失败（未知的错误）
   */
  public function modify_config($name, $new_value) {
    $row = $this->pattern($name);
    if($row === -1) 
      return false;
    //除去末尾的换行符
    $array = explode(',', trim($this->config_array[$row]));
    // 获得相应的值，去除);
    $value = substr($array[1], 0, -2);
    // print_r($value);
    $this->config_array[$row] = str_replace($value, $new_value, $this->config_array[$row]);

    // print_r($this->config_array[$row]);

    $this->write();
    return true;
    
  }

  /**
   * 添加一项
   * @deprecated 当配置文件为php文件时可以直接通过常量获得
   * @param  $name  名称
   * @return  string or int 
   */
  public function get_config_value($name) {
    return null;
  }
}