<?php
require_once 'library/utils/JSCompressor.php';
require_once 'library/utils/CSSCompressor.php';

class CompressPart extends Part
{
	private $expiresOffset;
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->expiresOffset = 3600 * 24 * 10;
	}
	
	protected function doAll()
	{
		$this->output = false;
		$debug = false;
		if(isset($_GET['debug']))
			$debug = true;
		if($this->url[0] == 'js')
			JSCompressor::compress($this->expiresOffset, $debug);
		if($this->url[0] == 'css')
			CSSCompressor::compress($this->expiresOffset, $debug);
		parent::doAll();
	}
}
?>