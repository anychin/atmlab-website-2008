<?php
class Config
{
	private $params = array();
	
	private static $instance = null;
	
	public static function getInstance()
	{
		if(self::$instance == null)
			self::$instance = new self();
		return self::$instance;
	}
	
	public function __get($param)
	{
		if(!isset($this->params[$param]))
			return null;
		else
			return $this->params[$param];
	}
	
	public function __set($param, $value)
	{
		$this->params[$param] = $value;
	}
}