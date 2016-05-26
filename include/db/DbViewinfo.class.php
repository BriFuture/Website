<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate: 04.xx
 * 实现View_info表
 * 具体操作交给C层
 * table: view_info
 * filename: Db_viewinfo.class.php
*/
if(!defined('VERSION')) {
  header('Location:/');
}

class Db_viewinfo {
  // private static $PRIMARY='id';
  public static $COLUMNS=['id','IP','SID', 'Browser', 'Login_Time'];
  private $db;
  private $table_name= 'view_info';

  function __construct() {
    $this->db = Db::getInstance();
    /*if($this->mysqli instanceof mysqli)
    {
      echo "connected!!!";
    }
    else
    {
      echo "not connect";
    }*/
  }

  /**
   * 添加View
   * @param $arr 输入的数组，由C层输入
  */
  function view_add(array $arr) {
    $query = 'INSERT INTO view_info (IP, SID, Browser, Login_Time) VALUES(
      inet_aton(\''.$arr['ip'].'\'),\''.$arr['sid'].'\',\''.$arr['browser'].'\', NOW())';
    //
    $result = $this->db->query($query);
  }

  /**
   * 获取view
   *     默认从第一项开始，最多十五条
   * @return 返回一个嵌套的数组，即便只有一条信息
  */
  function get_all_view($start = 0,$defnum = 15) {
    $selectspecs = array(
      'columns' => array(
        'id',
        'ip' => 'inet_ntoa(IP)',
        'SID',
        'Browser',
        'Login_Time'
      ),
      'source' => $this->table_name,
      'arguments' => null,
      'arraykey' => 'id',
      'limit' => '',
    );
    // print_r($selectspecs);
    // $query = 'SELECT id, inet_ntoa(IP), SID, Browser, Login_Time FROM view_info WHERE 1 ORDER BY id DESC ';
    // $query .= 'LIMIT '.($start*$defnum).','.$defnum;  //添加条数限制
    // $result = $this->mysqli->query($query);  //查询
    $result = $this->db->single_select($selectspecs);
    // print_r($result);
    return $result;
  }
}
