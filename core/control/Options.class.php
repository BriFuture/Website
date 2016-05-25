<?php 
/**
 * @author future <zhoujw@sunsmell.cc>
 * starttime: 05.05
 * lastmodifytime: 05.06
 * filename: Options.class.php
 * 通过option读取部分数据，而非数据库
 */
if(!defined('VERSION')) {
  header('Location:/');
  exit();
}

class Options {
  //定义常量
  const PERMIT_ALL    	= 150;
  const PERMIT_USERS  	= 120;
  const PERMIT_EDITORS  = 70;
  const PERMIT_ADMINS		= 50;
  const PERMIT_SUPERS 	= 0;

  public $options_cache;

  public $options_loaded;

  /**
   * 通过$names获取设置
   * @param  $names  名称
   * @return  返回names对应的值
   */
  public function get_option($names) {
  	//如果options还没有缓存，将options从数据库中取出
  	if(!$this->options_loaded)
  		$this->preload_options();

  	if(!$this->options_cache)
  	{
  		$this->load_options_result(array());
  	}
  }

  /**
   * 预加载option
   */
  public function preload_options() {
  	if(!isset($this->options_loaded) || !$this->options_loaded)  //没有加载option
  	{
  		$select_specs = array(
  			'options'	=> array(
  				'columns'    => array('title', 'content'),
  				'source'	 	 => 'options',
  				'arraykey' 	 => 'title',
  				'arrayvalue' => 'content',
  				),
  			'time'  	=> array( //读取数据库时间
  				'columns'		 => array('title' => "'db_time'", 'content' => 'UNIX_TIMESTAMP(NOW())'),
  				'arraykey'   => 'title',
  				'arrayvalue' => 'content',
  				),
  		);
      $db = Db::getInstance();
  		$this->load_options_results($db->multi_select($select_specs));
  	}
  }
	
	/**
   * 加载结果
   * 将所有结果加载到cache中
   * @param  $results  结果集
   */  
  public function load_options_results($results) {
  	foreach($results as $result)
    {
  		foreach($result as $name => $value)
      {
  			$this->options_cache[$name] = $value;
      }
    }

  	$this->options_loaded=true;
  }
	
	/**
   * 设置相应项的值
   * @param  项的名称
   * @param  新的值
   */
  public function set_option($name, $value, $todatabase=true) {
  	if($todatabase && isset($value))
    {

    }

  }
	
	/**
   * 重置
   * @param  $names  要重置的名称数组
   */
  public function reset_option($names) {
  	foreach($names as $name)
    {
  		$this->set_option($name, $this->default_option($name));
    }
  }
	
	/**
   * 返回默认的选项，在数据库中option没有相应的值时或者没有option时新建
   * @param  $name
   */
  public function default_option($name) {
  	$fixed_defaults = array(
  		'allow_change_username' => 1,
  		// 'allow_'
  	);

  	if(isset($fixed_defaults[$name]))
  		$value = $fixed_defaults[$name]
  	else
  		switch ($name) {
  			case 'value':
  				# code...
  				break;
  			
  			default:
  				# code...
  				break;
  		}
  }
	
}