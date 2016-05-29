<?php  
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 04.27
 * table: user
 * filename: DbUser.class.php
 * 操作user表
*/
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}

class DbUser {
  // const $COLUMNS = ['id', 'UID','name', 'pass', 'passslat','lever', 'email', 'contact', 'picture', 'score'];
  /**
   * 数据库对象
   */
  private $db;
  const TABLE_NAME = 'user';
  private $error;

  public function __construct() {
    $this->db = Db::getInstance();
  }

  /**
   * 添加user
   * @param $arr包含用户名 密码等
   * 随机生成特定格式的UID和passsalt
   */
  public function add_user(array $arr) {
    if(!$this->check_input($arr))
    {
      Base::fatal_error('数据有错误, ');
      return;
    }
    $query  = 'INSERT INTO user (UID, name, pass, passsalt, lever, email, contact, picture, score, created) VALUES $, $, $, $, $, $, $, $, $, NOW() ';
    $result = $this->db->query($query, $this->formed_UID(), $arr['name'], $arr['pass'], $this->get_passsalt(), $arr['lever'], $arr['email'], $arr['contact'], $picture, $score);
    return $this->db->last_insert_id();
  }

  /**
   * 需要先将其它表中以UID为外键的数据删除，然后再删除用户
   * @param  $who  UID
   */
  public function delete($who) {

  }

  /**
   * @param $who UID
   * 根据UID更新某些信息
   */
  public function update($who, array $arr) {

  }

  /**
   * 根据UID返回相应的数据
   */
  public function select_info($who) {

  }

  /**
   * 随机生成passsalt
   * @return  随机码
   */
  private function get_passsalt($format=null) {
    $security = new Security();
    return $security->random(8);
  } 

  /**
   * 生成特定格式的UID, 目前为十位，两位的年份，两位的月份，6位随机数
   * @return  
   */
  private function formed_UID() {
    //获取年份（两位），月份（一位，十六进制）
    $year = substr(date('o'), -2);
    $month = date('m');
    $random_num = sprintf('%06d', mt_rand(0,999999));
    $uid = $year.$month.$random_num;

  }

  /**
   * 检查输入,防止错误的数据
   * @param  $arr  输入的数据
   * @return  bool  
   */
  private function check_input($arr, $for=null) {
    if(!isset($arr['name']))
    {
      $this->error .= ' 没有设置用户名！ ';
    }
    if(!isset($arr['pass']))
    {
      $this->error .= ' 没有设置密码！ ';
    }
    if(!isset($arr['email']))
    {
      $this->error .= ' 没有设置邮箱！ ';
    }
  }
}