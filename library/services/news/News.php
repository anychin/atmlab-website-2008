<?php
class News
{
	private $news = null;
	
	private $image = null;
	
	private $gallery = null;
	
	public function __construct($news)
	{
		$this->news = $news;
	}
	
	public function __get($field)
	{
		if($field != 'gallery' && $field != 'image')
			return $this->news->$field;
		if($field == 'gallery')
		{
			$this->getGallery();
			return $this->gallery;
		}
		if($field == 'image')
		{
			$this->getImage();
			return $this->image;
		}
		return null;
	}
	
	protected function getImage()
	{
		if($this->image != null ||  ! $this->news->image)
			return $this->image;
		$this->image = new Image(DAOService::instance('ImageService')->get($this->news->image));
		return $this->image;
	}
	
	private function getGallery()
	{
		if($this->news != null ||  ! $this->news->gallery)
			return array();
		$this->gallery = DAOService::instance('PhotoService')->getByGallery($this->news->gallery);
		return $this->gallery;
	}
}

?>
