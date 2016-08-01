<?php  
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 06.16
 * table: image
 * filename: DbImages.class.php
 * 记录图片 hash 和位置的对应关系
*/
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}

class DbImages {
  // const COLUMNS = ['ID', 'name', 'style', 'create_time', 'permission'];
  private $db;
  // const TABLE_NAME = 'images';

  public function __construct() {
    $this->db = Db::getInstance();
    // var_dump($this->db);
  }
  
  /**
   * 添加图片记录
   * @param  
   *  $image  array
   *    $image['columns']['name']        图片名
   *    $iamge['columns']['path']        图片在服务器上的路径
   *    $image['columns']['group']       分组
   *    $image['columns']['description'] 描述图片
   *    $image['columns']['addons']      额外信息
   * @return 
   **/
  public function create($image) {
    return PatternDb::pattern_insert_sql($image, 'images', true);
  }

  /**
   * 更新记录
   */
  public function update($image) {
    // $query_str = 'UPDATE `images` SET `img_path`=$, `img_group`=$, `description`=$ WHERE `name`=$';
    // $this->db->query($query_str, $path, $group, $description, $image['name']);
    return PatternDb::pattern_update_sql($image, 'images', true);
  }

  /**
   * 删除记录
   * @param  $image  
   *          array('where_column' => 'where_value')  or value
   */
  public function delete($image) {
    if(is_array($image)) {
      $where = array_keys($image);
      $where = $where[0];
      $value = array_values($image);
      $value = $value[0];
      $query_str = 'DELETE FROM `images` WHERE `'.$where.'` = '.(is_numeric($value) ? ' # ' :' $ ');
      return $this->db->query($query_str, $value);
    } else {
      $query_str = 'DELETE FROM `images` WHERE `img_id` = #';
      return $this->db->query($query_str, $image);
    }
  }

  /**
   * 通过名称得到图片
   */
  public function get_one_image($name) {
    $query_str = 'SELECT `img_id`, `img_name`, `img_path`, `img_group`, `description`, `addons` FROM `images` WHERE `img_name`=$';
    $result = $this->db->query($query_str, $name);

    return Db::get_one_assoc($result);
  }

  /**
   * 通过组别获得图片
   */
  public function get_images_by_group($group) {
    $query_str = 'SELECT `img_id`, `img_name`, `img_path`, `img_group`, `description`, `addons` FROM `images` WHERE `img_group`=$';
    $result = $this->db->query($query_str, $group);

    return Db::get_all_assoc($result);
  }

  /**
   * 获取所有图片的信息
   */
  public function get_all($from=0, $items=30) {
    $query_str = 'SELECT `img_id`, `img_name`, `img_path`, `img_group`, `description`, `addons` FROM `images` LIMIT #, #';
    $result = $this->db->query($query_str, $from, $items);
    return Db::get_all_assoc($result);
  }
}