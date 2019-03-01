<?php
require_once 'CompressTool.php';

class JSCompressor extends CompressTool 
{
	public static function compress($expire, $debug = false, $param = 'scripts')
	{
		self::$contentType = 'text/javascript';
		self::$dir = 'js';
		self::$fileDelimiter = ' ; ';
		self::$fileExtension = 'js';
		parent::compress($expire, $debug, $param);
	}
}
?>