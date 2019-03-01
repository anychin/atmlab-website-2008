<?php
class Utils_JSCompressor extends Utils_CompressTool 
{
	public static function compress($param, $debug = false)
	{
		self::$dir = 'js';
		self::$fileDelimiter = ' ; ';
		self::$fileExtension = 'js';
		return parent::compress(__CLASS__, $param, $debug);
	}
}
?>