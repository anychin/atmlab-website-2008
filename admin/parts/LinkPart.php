<?php
require_once ('services/article/link/LinkService.php');

class LinkPart extends Part
{
	private $service;
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->service = DAOService::instance('LinkService');
	}
	
	public function doAll()
	{
		$this->output = false;
		$this->setNoCahceHeaders();
		$this->parseUrl();
	}
	
	private function parseUrl()
	{
		if($this->url[0] == 'add')
			$this->service->addLink($_GET['article'], $_GET['link']);
		if($this->url[0] == 'delete')
			$this->service->deleteLink($_GET['article'], $_GET['link']);
		if($this->url[0] == 'delete-other')
			$this->service->deleteLink($_GET['article'], $_GET['link']);
	}
}

?>
