<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate: 05.19
 * modifydate:05.xx
 * filename: Format.class.php
 * 格式化字符串输出
*/

if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Format {

  /**
   * 将时间转换为字符串显示
   * @param  $seconds  秒为单位的时间
   * @return 返回格式化后的时间字符串
   */
  public function time_to_string($seconds) {
    $seconds = max($seconds, 1);

    $scales  = array(
      31557600 => array('main/1_year'    , 'main/x_years'    ),
      2629800  => array('main/1_month'   , 'main/x_months'   ),
      604800   => array('main/1_week'    , 'main/x_weeks'    ),
      86400    => array('main/1_day'     , 'main/x_days'     ),
      3600     => array('main/1_hour'    , 'main/x_hours'    ),
      60       => array('main/1_minute'  , 'main/x_minutes'  ),
      1        => array('main/1_second'  , 'main/x_seconds'  ),
    );

    foreach($scales as $scale => $phrases) {
      if($seconds >= $scale)
      {
        $count = floor($seconds/$scale);

        if($count == 1)
        {
          $string = $phrases[0];
        }
        else
        {
          $string = $this->format_lang($phrases[1], $count);
        }
      }
    }

    return $string;
  }

  /**
   * 将字符串中的x替换成数字
   * @param  $str   需要替换的字符串
   * @param  $count 数字
   */
  public function format_lang($str, $count) {
    return str_replace('x', $count, $str);
  }

}