<?php  
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate 07.31
 * filename: PatternDb.class.php
 * 模板化常用的 SQL 语句
 *  SQL 中的函数无法调用。
*/
if(!defined('VERSION')) 
{
  header('Location: /');
  exit();
}

class PatternDb {
  /**
   * 返回插入数据的 SQL 语句或者执行例化的 SQL 语句
   * @param  
   *  $insert  二维数组
   *    $insert['columns']    array('column_name' => 'value', ...)
   *    $insert['spec_columns']    array('column_name' => 'value', ...)  专门的 sql 函数采用这个数组
   *    $insert['table']   要插入的表
   *  $table   要插入的表，优先级高
   *  $excute  是否执行当前 SQL 语句，执行则不再返回 sql 语句。
   */
  public static function pattern_insert_sql($insert, $table=null, $excute=false) {
    if(!is_null($table)) {
      $insert_table = $table;
    } else  if(isset($insert['table'])) {
      $insert_table = $insert['table'];
    } else {
      // Base::debug('No table specified');
      Base::fatal_error('No table specified');
      // return;
    }

    $query_str = 'INSERT INTO `'.$insert_table.'` (';

    foreach ($insert['columns'] as $column_name => $column_value) {
      $query_str .= '`'.$column_name.'`, ';
    }

    //为了能够直接写入 SQL 函数 而加的循环
    if(isset($insert['spec_columns']))
      foreach ($insert['spec_columns'] as $column_name => $column_value) {
        $query_str .= '`'.$column_name.'`, ';
      }

    $query_str = substr($query_str, 0, -2).') VALUES (';

    //添加 $ 或者 #
    foreach ($insert['columns'] as $column_value) {
      $query_str .= is_numeric($column_value) ? '#, ':'$, ';
    }
    //为了能够直接写入 SQL 函数 而加的循环
    if(isset($insert['spec_columns']))
      foreach ($insert['spec_columns'] as $column_value) {
        $query_str .= $column_value.', ';
      }
    // $query_str .= str_repeat('$, ', count($insert['columns']));
    $query_str = substr($query_str, 0, -2).')';

    $db = Db::getInstance();
    $pattern_str = $db->substitude($query_str, array_values($insert['columns']));

    if(!$excute)  {
      return $pattern_str;
    } else {
      return $db->query($pattern_str);
    }
  }

  /**
   * 返回更新数据的 SQL 语句或者执行例化的 SQL 语句
   * @param  
   *  $update
   *    $update['columns']  array('column_name' => 'some value', ...)
   *    $update['spec_columns']    array('column_name' => 'value', ...)  专门的 sql 函数采用这个数组
   *    $update['where']    array(array('column' => 'some value', 'op' => 'some value', 'value' => *      'some value', 'next' => 'and or '), ...)
   */
  public static function pattern_update_sql($update, $table=null, $excute=false) {
    if(!is_null($table)) {
      $update_table = $table;
    } else  if(isset($update['table'])) {
      $update_table = $update['table'];
    } else {
      Base::fatal_error('No table specified');
    }

    $query_str = 'UPDATE `'.$update_table.'` SET ';

    foreach ($update['columns'] as $column_name => $column_value) {
      $query_str .= '`'.$column_name.'` = '.(is_numeric($column_value) ? '#' : '$').', ';
    }
     //为了能够直接写入 SQL 函数 而加的循环
    if(isset($update['spec_columns']))
      foreach (@$update['spec_columns'] as $column_name => $column_value) {
        $query_str .= '`'.$column_name.'` = '.$column_value.', ';
      }
    
    $query_str = substr($query_str, 0, -2).' WHERE ';

    foreach ($update['where'] as $where) {
      $query_str .= '`'.$where['column'].'`'. $where['op'].(is_numeric($where['value']) ? ' # ': ' $ ').@$where['next'];
    }


    $update_value = array_values($update['columns']);
    foreach ($update['where'] as $where) {
      $update_value[] = $where['value'];
    }

    $db = Db::getInstance();
    $pattern_str = $db->substitude($query_str, $update_value);

    if(!$excute)  {
      return $pattern_str;
    } else {
      return $db->query($pattern_str);
    }
  }

  /**
   * 返回查找数据的 SQL 语句或者执行例化的 SQL 语句
   * @param  
   *  $select
   *    $select['columns']  array('some columns', ...)
   *    $select['spec_columns']  array('some columns', ...)
   *    $select['where']  array(array('column' => 'some value', 'op' => 'some value', 'value' => *      'some value', 'next' => 'and or '), ...)
   */
  public static function pattern_select_sql($select, $table=null, $excute=false) {
    if(!is_null($table)) {
      $select_table = $table;
    } else  if(isset($update['table'])) {
      $select_table = $update['table'];
    } else {
      Base::fatal_error('No table specified');
    }

    $query_str = 'SELECT ';

    foreach ($select['columns'] as $column) {
      $query_str .= '`'.$column.'`, ';
    }
    //为了能够直接写入 SQL 函数而加的循环
    if(isset($select['spec_columns']))
      foreach ($select['spec_columns'] as $column) {
        $query_str .= $column.', ';
      }
    
    $query_str = substr($query_str, 0, -2).' WHERE ';

    foreach ($select['where'] as $where) {
      $query_str .= '`'.$where['column'].'` '. $where['op'].(is_numeric($where['value']) ? ' # ': ' $ ').@$where['next'];
    }


    $select_where = array_values($select['where']);

    $db = Db::getInstance();
    $pattern_str = $db->substitude($query_str, $select_where);

    if(!$excute)  {
      return $pattern_str;
    } else {
      return $db->query($pattern_str);
    }
  }
}