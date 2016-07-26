<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate:0521
 * filename: Info.class.php
 * 有关Info页面的操作
 * 
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Info extends Page{

  /**
   * ViewInfo页面
   * 默认显示defaults函数渲染的函数
   * @param  $call 渲染页面需要的不同逻辑
   * @param  $msg  附加的信息
   */
  public function render($msg=null) {
    $this->inc(__CLASS__);
  }

  public function view_info_content($view) {
    echo '<table class="table table-striped table-hover table-responsive">';
    $counti=0;
    if(count($view) !==0)
      foreach($view as $key1 => $value1) {
        if($counti%10===0)
        {
          echo '<tr>';
          foreach (Db_viewinfo::$COLUMNS as $key => $value) {
            echo '<th class="info">'.$value.'</th>';
          }
          echo '</tr>';
          $counti=0;  
        }
        echo '<tr>';
        foreach ($value1 as $key => $value) {
          echo '<td>'.$value.'</td>';
        }
        echo '</tr>';
        $counti++;
      } 
    echo "</table>";
  }
}