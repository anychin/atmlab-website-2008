<?php

class CompressTool
{
	protected static $contentType;
	
	protected static $fileExtension;

	protected static $dir;
	
	protected static $fileDelimiter;
	
	public static function compress($expire, $debug = false, $param = 'scripts')
	{
		header('Content-type: '.self::$contentType);
		header('Vary: Accept-Encoding');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expire) . ' GMT');
		$cacheKey = md5($_GET[$param]);
		$encodings = array();
		$supportsGzip = false;
		$enc = '';
		if(isset($_SERVER['HTTP_ACCEPT_ENCODING']))
			$encodings = explode(',', strtolower(preg_replace('/\s+/', '', $_SERVER['HTTP_ACCEPT_ENCODING'])));
		if((in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') &&  ! ini_get('zlib.output_compression'))
		{
			$enc = in_array('x-gzip', $encodings) ? "x-gzip" : 'gzip';
			$supportsGzip = true;
		}
		$supportsGzip = $supportsGzip && !$debug;
		$cacheFile = Config::get('gzipDir') . $cacheKey . '.gz';
		if( ! $supportsGzip)
			$cacheFile = Config::get('gzipDir') . $cacheKey . '.' . self::$fileExtension;
		if($supportsGzip)
			header('Content-Encoding: ' . $enc);
		if(file_exists($cacheFile))
		{
			echo file_get_contents($cacheFile);
			return;
		}
		$content = '';
		$scripts = explode(',', $_GET[$param]);
		$d = preg_replace("/\\/$/", "", SITE_DIR);
		for($i = 0; $i < count($scripts); $i++)
		{
			$path = $d.$scripts[$i] . '.' . self::$fileExtension;
			if($scripts[$i][0] != '/')
				$path = realpath(self::$dir . '/' . $scripts[$i] . '.' . self::$fileExtension);
			$content .= file_get_contents($path) . self::$fileDelimiter;
		}
		if($supportsGzip)
			$content = gzencode($content, 9, FORCE_GZIP);
		if(!$debug)
			file_put_contents($cacheFile, $content);
		echo $content;
	}
}

?>