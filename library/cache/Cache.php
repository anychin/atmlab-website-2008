<?php
require_once('lite/Lite.php');
require_once('lite/Function.php');
require_once('lite/Output.php');

class Cache
{
	private $options;
	
	public $lite;
	
	public $f;
	
	public $out;
	
	public function __construct()
	{
		$this->options = array('cacheDir' => Config::get('cacheDir'), 'lifeTime' => Config::get('lifeTime'), 'readControlType'=>'md5');
		$this->lite = new Cache_Lite($this->options);
		$this->out = new Cache_Lite_Output($this->options);
		$this->f = new Cache_Lite_Function($this->options);
	}
	
	public function get($id, $group = 'default', $doNotTestCacheValidity = false)
	{
		return $this->lite->get($id, $group, $doNotTestCacheValidity);
	}
	
	public function save($data, $id = NULL, $group = 'default')
	{
		return $this->lite->save($data, $id, $group);
	}
	
	public function clean($group = false, $mode = 'ingroup')
    {
    	return $this->lite->clean($group, $mode);
    }
    
    public function remove($id, $group = 'default', $checkbeforeunlink = false)
    {
    	return $this->lite->remove($id, $group, $checkbeforeunlink);
    }
	
	public function start($id, $group = 'default', $doNotTestCacheValidity = false)
	{
		return $this->out->start($id, $group, $doNotTestCacheValidity);
	}
	
	public function end()
	{
		$this->out-end();
	}
	
	/*Access to function methods (call, drop) via f variable*/		
}
?>