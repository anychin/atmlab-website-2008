<?php
require_once ('utils/Upload.php');

class EditorPart extends Part
{
	private $folder = 'media/';
	
	private $uploader;
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->folder = SITE_DIR.$this->folder;
		$this->templateFile = 'editor/editor.phtml';
	}
	
	public function doAll()
	{
		parent::doAll();
		$this->parseUrl();
	}
	
	private function parseUrl()
	{
		if($this->url[0] == 'list')
		{
			Template::add('files', $this->getList());
			return;
		}
		if($this->url[0] == 'upload')
		{
			$this->output = false;
			$this->uploader = new Upload($_FILES['flashfile']);
			$this->uploader->process($this->folder);
			if($this->uploader->processed)
				echo '<?xml version="1.0"?><file name="'.$this->uploader->file_dst_name.'"/>';
		}
		if($this->url[0] == 'mce')
		{
			$this->output = false;
			$this->getTiny();
		}
	}
	
	private function getTiny()
	{
		@error_reporting(E_ERROR | E_WARNING | E_PARSE);
		$plugins = explode(',', $this->getParam('plugins', ''));
		$languages = explode(',', $this->getParam('languages', ''));
		$themes = explode(',', $this->getParam('themes', ''));
		$diskCache = $this->getParam("diskcache", "") == "true";
		$isJS = $this->getParam("js", "") == "true";
		$compress = $this->getParam("compress", "true") == "true";
		$core = $this->getParam("core", "true") == "true";
		$suffix = $this->getParam("suffix", "_src") == "_src" ? "_src" : "";
		//$cachePath = realpath('.').'/editor';
		$cachePath = Config::get('gzipDir');
		$expiresOffset = 3600 * 24 * 10;
		$content = "";
		$encodings = array();
		$supportsGzip = false;
		$enc = "";
		$cacheKey = "";
	
		$custom = array();
	
		header("Content-type: text/javascript");
		header("Vary: Accept-Encoding");
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
	
		if (!$isJS) {
			echo $this->getFileContents("tiny_mce_gzip.js");
			echo "tinyMCE_GZ.init({});";
			die();
		}
	
		if ($diskCache) {
			if (!$cachePath)
				die("alert('Real path failed.');");
	
			$cacheKey = $this->getParam("plugins", "") . $this->getParam("languages", "") . $this->getParam("themes", "") . $suffix;
	
			foreach ($custom as $file)
				$cacheKey .= $file;
	
			$cacheKey = md5($cacheKey);
	
			if ($compress)
				$cacheFile = $cachePath . "tiny_mce_" . $cacheKey . ".gz";
			else
				$cacheFile = $cachePath . "tiny_mce_" . $cacheKey . ".js";
		}
	
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
			$encodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));
	
		if ((in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression')) {
			$enc = in_array('x-gzip', $encodings) ? "x-gzip" : "gzip";
			$supportsGzip = true;
		}
		
		if ($diskCache && $supportsGzip && file_exists($cacheFile)) {
			if ($compress)
				header("Content-Encoding: " . $enc);
			echo $this->getFileContents($cacheFile);
			die();
		}
	
		if ($core == "true") {
			$content .= $this->getFileContents("editor/tiny_mce" . $suffix . ".js");
			$content .= "tinyMCE_GZ.start();";
		}
	
		foreach ($languages as $lang)
			$content .= $this->getFileContents("editor/langs/" . $lang . ".js");
	
		foreach ($themes as $theme) {
			$content .= $this->getFileContents( "editor/themes/" . $theme . "/editor_template" . $suffix . ".js");
	
			foreach ($languages as $lang)
				$content .= $this->getFileContents("editor/themes/" . $theme . "/langs/" . $lang . ".js");
		}
	
		foreach ($plugins as $plugin) {
			$content .= $this->getFileContents("editor/plugins/" . $plugin . "/editor_plugin" . $suffix . ".js");
	
			foreach ($languages as $lang)
				$content .= $this->getFileContents("editor/plugins/" . $plugin . "/langs/" . $lang . ".js");
		}
	
		foreach ($custom as $file)
			$content .= $this->getFileContents($file);
	
		if ($core == "true")
			$content .= "tinyMCE_GZ.end();";
	
		if ($supportsGzip) {
			if ($compress) {
				header("Content-Encoding: " . $enc);
				$cacheData = gzencode($content, 9, FORCE_GZIP);
			} else
				$cacheData = $content;
	
			if ($diskCache && $cacheKey != "")
				$this->putFileContents($cacheFile, $cacheData);
	
			echo $cacheData;
		} else {
			echo $content;
		}
	}
	
	private function getList()
	{
		$files = array();
		$dir = opendir($this->folder);
		if($dir){
			while($file = readdir($dir))
				if($file != '.' && $file != '..' && strpos($file, '.') > 0)
					$files[] =$file;
			closedir($dir);
		}
		return $files;
	}
	
	/**** tiny ****/
	private function getParam($name, $def = false) {
		if (!isset($_GET[$name]))
			return $def;
		return preg_replace("/[^0-9a-z\-_,]+/i", "", $_GET[$name]);
	}
	
	function getFileContents($path) {
		$path = realpath($path);
		if (!$path || !@is_file($path))
			return '';
		return file_get_contents($path);
	}

	private function putFileContents($path, $content) {
		return @file_put_contents($path, $content);
	}
}

?>
