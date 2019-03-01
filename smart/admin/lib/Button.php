<?php
class Lib_Button
{
	public $type = '';
	
	public $url = '';
	
	public $message = '';
	
	public $redirrectUrl = '';
	
	public $data;
	
	public function __construct($type, $url, $message = '', $redirrectUrl = '', $data = array())
	{
		$this->type = $type;
		$this->url = $url;
		$this->message = $message;
		$this->redirrectUrl = $redirrectUrl;
		$this->data = $data;
		if($this->url)
			$this->url = Config::getInstance()->adminUrl . $this->url;
	}
}
?>