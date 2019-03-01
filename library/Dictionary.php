<?php
class Dictionary
{
	private static $messages = array();
	
	public static $language = "";
	
	public static function get($message)
	{
		$code = self::$language.".".$message;
		if(isset(self::$messages[$code])){
			return self::$messages[$code];
		}
		$code = Config::get("defaultLanguage").".".$message;
		if(isset(self::$messages[$code])){
			return self::$messages[$code];
		}
		return $message;
	}
	
	public static function add($message, $value)
	{
		self::$messages[$message] = $value;
	}
}
?>
