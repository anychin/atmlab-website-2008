<?php
class Utils_CompressTool
{
	protected static $fileExtension;

	protected static $dir;
	
	protected static $fileDelimiter;
	
	protected static $instance = null;
	
	public static function compress($clazz, $param, $debug = false)
	{
		self::$instance = new $clazz();
		$cacheKey = md5($param);
		$cacheFile = Config::getInstance()->gzipDir . $cacheKey . '.' . self::$fileExtension;
		if($debug || !file_exists($cacheFile))
			self::$instance->concat($param);
		return '/'.str_replace(SITE_DIR, '', $cacheFile);
	}
	
	protected function concat($param)
	{
		$cacheKey = md5($param);
		$cacheFile = Config::getInstance()->gzipDir . $cacheKey . '.' . self::$fileExtension;
		$scripts = explode(',', $param);
		$d = preg_replace("/\\/$/", "", SITE_DIR);
		$f = fopen($cacheFile, 'w');
		for($i = 0; $i < count($scripts); $i++)
		{
			$path = $d.$scripts[$i] . '.' . self::$fileExtension;
			if($scripts[$i][0] != '/')
				$path = realpath(self::$dir . '/' . $scripts[$i] . '.' . self::$fileExtension);
			fwrite($f, self::$instance->processBody(file_get_contents($path)) . self::$fileDelimiter);
		}
		fclose($f);
	}
	
	protected function processBody($body)
	{
		return $body;
	}
}

?>