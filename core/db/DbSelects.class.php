<?php  
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 05.08
 * table: selects
 * filename: DbSelects.class.php
 * 操作selects表
*/

if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}

class DbSelects {
  const COLUMNS = [];
  private $db;
  private $table_name = "selects";

  function __construct() {
    $this->db = Db::newInstance();
  }

  public function selct_with_pending() {

  }

  public function queue_pending_select() {

  }

  public function get_pending_result() {

  }

  public function flush_pending_result() {

  }

  public function selectspec_count($selectspec) {

  }

  public function posts_basic_selectspec($voteuserid=null, $full=false, $user=true) {

  }

  public function add_selectspec_opset(&$selectspec, $poststable, $fromupdated=false, $full=false) {

  }

  public function add_selectspec_ousers(&$selectspec, $userstable, $pointstable) {

  }

  public function slugs_to_backpath($categoryslugs) {

  }

  public function categoryslugs_sql_args($categoryslugs, &$arguments) {

  }

  public function qs_selectspec($voteuserid, $sort, $start, $categoryslugs=null, $createip=null, $specialtype=false, $full=false, $count=null)
  {
    
  }

}