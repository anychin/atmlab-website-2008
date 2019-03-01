<?php
require_once 'CompressTool.php';

class CSSCompressor extends CompressTool 
{
	public static function compress($expire, $debug = false, $param = 'scripts')
	{
		self::$contentType = 'text/css';
		self::$dir = 'css';
		self::$fileDelimiter = '\n';
		self::$fileExtension = 'css';
		parent::compress($expire, $debug, $param);
	}
}

?>