<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * startdate: 05.19
 * modifydate:05.xx
 * filename: Limit.class.php
 * 对访问做出限制，比如尝试登陆次数过多，行为异常等等，就需要限制行为
*/
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Limit {
  const LIMIT_LOGIN = 'L';

  /**
   * 返回剩余的尝试次数
   * @param  $action  行为
   * @param  number  剩余次数
   */
  public function user_limit_remaining($action) {
    //
    $dbLimits = Factory::getObject('DbLimits');
    $dbUsers   = Factory::getObject('DbUsers');
    $base     = new Base();
    $user_limits = $dbLimits->get_limit($dbUsers->get_login_userid, $base->get_remote_ip(), $aciton);
    $ip_limits   = $dbLimits->get_limit($dbUsers->get_login_userid, $base->get_remote_ip(), $aciton);

    return $this->calc_limits_remaining($action, $user_limits, $ip_limits);
  }

  /**
   * 计算剩余的限制次数
   * @param  $action        行为
   * @param  $user_limits   用户限制
   * @param  $ip_limits     ip限制
   */
  public function calc_limits_remaining($action, $user_limits, $ip_limits)  {
    switch ($action) {
      case 'value':
        # code...
        break;
      case self::LIMIT_LOGIN:
        break;
      default:
        $base = new Base();
        $base->fatal_error('未知的限制代码，代码为：'.$action);
        break;
    }
    $options = Factory::getObject('Options');
    $period = (int) ($options->get_option('db_time') / 3600);

    return max(0, 
      min(
        $usermax - ((@$user_limits['period'] == $period) ? $user_limits['count'] : 0), 
        $ipmax   - ((@$ip_limits['period']   == $period) ? $ip_limits['count']   : 0)
      )
    );
  }

  /**
   * 判断远程ip是否被屏蔽
   * @return  bool
   *      true  被屏蔽  false  没有被屏蔽
   */
  public function is_ip_blocked() {
    $options = Factory::getObject('Options');
    $block_ip_clauses = $this->block_ips_explode($options->get_option('block_ips_write'));

    $base = new Base();
    foreach($block_ip_clauses as $block_ip_clause) {
      //检查是否被屏蔽
      if($this->block_ip_match($base->get_remote_ip(), $block_ip_clause))
      {
        return true;
      }
    }

    return false;
  }

  /**
   * 将屏蔽的ip string分离成数组
   */
  public function block_ips_explode($block_ip_string) {
    //将屏蔽的错误ip替换掉
    $block_ip_string = preg_replace('/\s*\-\s*/', '-', $block_ip_string);
    //将屏蔽的ip分散成数组
    return preg_split('/[^0-9\.\-\*]/', $block_ip_string, -1, PREG_SPLIT_NO_EMPTY);
  }

  /**
   * 匹配被屏蔽的ip
   * @param  $ip  要搜索的ip地址
   * @param  $block_ip_clause  被屏蔽的原因
   * @return  bool
   *      true  匹配到被屏蔽的ip
   *      false 未匹配到
   */
  public function block_ip_match($ip, $block_ip_clause) {
    //先将string类型的字符串转换成long再转换成string
    if(long2ip(ip2long($ip)) == $ip) {
      if(preg_match('/^(.*)\-(.*)$/'), $block_ip_clause, $matches)
      {
        if( (long2ip(ip2long($matches[1])) == $matches[1]) && (long2ip(ip2long($matches[2])) == $matches[2]) )
        {
          $iplong   = sprintf('%u', ip2long($ip));
          $end1long = sprintf('%u', ip2long($matches[1]));
          $end2long = sprintf('%u', ip2long($matches[2]));

          return ( ($iplong >= $end1long) && ($iplong <= $end2long) || (($iplong >= $end2long) && ($iplong <= $end1long)) );
        }
      }
      elseif(strlen($block_ip_clause))
      {
        return preg_match('/^'.str_replace('\\*', '[0-9]+', preg_quote($block_ip_clause, '/')).'$/', $ip) > 0;
      }
    }

    return false;
  }

  /**
   * 增加限制
   * @param  $userid  用户id
   * @param  $action  行为
   */
  public function limits_increase($userid, $action) {
    $options = Factory::getObject('Options');
    $period = (int) ($options->get_option('db_time') / 3600);

    $dbLimits = Factory::getObject('DbLimits');

    //如果用户已经登陆，记录登陆的id
    if(isset($userid))
    {
      $dbLimits->add_user($userid, $action, $period, 1);
    }

    $base = new Base();
    //记录远程ip
    $dbLimits->add_ip($base->get_remote_ip(), $action, $period, 1);
  }

}