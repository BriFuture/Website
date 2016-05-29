<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime: 05.02
 * lastmodifytime: 05.06
 * filename: DbMessage.class.php
 * 操作消息的数据库
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class DbMessages {
  // const $COLUMNS = ['fromuserid', 'touserid', 'content', 'type', 'format', 'fromhidden', 'tohidden'];
  private $db;
  const TABLE_NAME = 'messages';

  function __construct() {
    $this->db = Db::getInstance();
  }

  /**
   * 创建消息
   * @param  $fromuserid  发送者的id
   * @param  $touserid    接受者的id
   * @param  $content     消息内容
   * @param  $format      纯文本或者html
   * @param  $public      是否公开
   * @return  最后插入的id
   */
  public function create_message($fromuserid, $touserid, $content, $format, $public=false) {
    $query = 'INSERT INTO messages (type, fromuserid, touserid, content, format, created) VALUES ($, $, $, $, $, NOW())';

    $this->db->query($query, $public ? 'PUBLIC' : 'PRIVATE', $fromuserid, $touserid, $content, $format);
    return $this->db->last_insert_id();
  }

  /**
   * 设置消息已读或者隐藏消息
   * @param  $messageid  消息id
   * @param  $box  
   */
  public function message_user_hide($messageid, $box) {
    //选择
    $field = ($box === 'inbox' ? 'tohidden' : 'fromhidden');

    $query = "UPDATE messages SET $filed=1 WHERE messageid=#";
    $this->db->query($query, $messageid);
  }

  /**
   * 删除消息
   * @param  $messageid  
   * @param  $public
   *
   */
  public function message_delete($messageid, $public=true) {
    //where情况
    $clause = $public ? '' : ' AND fromhidden=1 AND tohidden=1';

    $query  = 'DELETE FROM messages WHERE messageid=#'.$clause; 
    $this->db->query($query, $messageid);
  }

  /**
   * @param  $userid
   */
  public function user_recount_posts($userid) {
    if($this->db->should_update_counts())
    {
      $query = "UPDATE users AS x, (SELECT COUNT(*) AS wallposts FROM message WHERE touserid=# AND type='PUBLIC') AS a set x.";
      $this->db_query()
    }
  }
}