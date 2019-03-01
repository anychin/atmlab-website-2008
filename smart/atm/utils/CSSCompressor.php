<?php
class Utils_CSSCompressor extends Utils_CompressTool 
{
	public static function compress($param, $debug = false)
	{
		self::$dir = 'css';
		self::$fileDelimiter = "\n";
		self::$fileExtension = 'css';
		return parent::compress(__CLASS__, $param, $debug);
	}
	
	protected function processBody($body)
	{
		if(Config::get('subdomains') == 0)
			return $body;
		$body = preg_replace_callback('/\\/images\\//', 'CSSCompressor::substitute', $body);
		return $body;
	}
	
	private static function substitute($matches){
		return 'http://img'.rand(1, Config::getInstance()->subdomains).'.'.Config::getInstance()->server.'/images/';
	}
}
?>