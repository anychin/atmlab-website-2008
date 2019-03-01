<?php
require_once 'PhotoService.php';
require_once 'Gallery.php';

class PhotoGalleryService extends DAOService 
{
	private $photoService;
	
	public function __construct()
	{
		parent::__construct('photogallery');
		$this->photoService = DAOService::instance('PhotoService');
	}
	
	public function getByUrl($url)
	{
		return $this->get(0, Expression::eq("url",$url));
	}
	
	public function create()
	{
		return $this->insert(array(description=>"", title=>"Новая фотогаллерея"));
	}
	
	public function delete($id)
	{
		$this->photoService->delete(Expression::eq("gallery", $id));
		parent::delete($id);
	}
}
?>