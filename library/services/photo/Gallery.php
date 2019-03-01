<?php
require_once 'Photo.php';

class Gallery
{
	private $photos = null;
	
	private $gallery = null;
	
	public function __construct($gallery)
	{
		$this->gallery = $gallery;
	}
	
	public function __get($field)
	{
		if($field != 'gallery' && $field != 'photos')
			return $this->gallery->$field;
		if($field == 'gallery')
			return $this->gallery;
		if($field == 'photos')
		{
			$this->getPhotos();
			return $this->photos;
		}
		return null;
	}
	
	protected function getPhotos()
	{
		if($this->photos != null)
			return;
		$photos = DAOService::instance('PhotoService')->getByGallery($this->gallery->id);
		$this->photos = array();
		for($i = 0; $i < count($photos); $i++)
			$this->photos[] = new Photo($photos[$i]);
	}
}

?>
