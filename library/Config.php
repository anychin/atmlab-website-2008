<?php
class Config
{
	private static $params = array();
	
	public static function get($param)
	{
		return self::$params[$param];
	}
	
	public static function set($param, $value)
	{
		self::$params[$param] = $value;
	}
}
?>
