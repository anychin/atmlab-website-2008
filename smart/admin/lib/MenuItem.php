<?php
class Lib_MenuItem
{
	public $name = '';
	
	public $url = null;
	
	public $subItems = null;
	
	public $order = 0;
	
	public function __construct($name, $url, $order = 0, $subItems = null)
	{
		$this->name = $name;
		$this->url = $url;
		$this->order = $order;
		$this->subItems = $subItems;
	}
}
?>