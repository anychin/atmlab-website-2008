<?php
class Template
{
	private static $template = array();
	
	public static function add($key, $data)
	{
		self::$template[$key] = $data;
	}
	
	public static function get($key)
	{
		return self::$template[$key];
	}
	
	public static function getAll()
	{
		return self::$template;
	}
}

?>
