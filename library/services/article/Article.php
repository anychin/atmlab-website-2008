<?php

class Article
{
	private $article = null;
	
	private $image = null;
	
	private $gallery = null;
	
	public function __construct($article)
	{
		$this->article = $article;
	}
	
	public function __get($field)
	{
		if($field == 'image')
			return $this->getImage();
		if($field == 'gal')
			return $this->getGallery();
		return $this->article->$field;
	}
	
	private function getImage()
	{
		if($this->image != null ||  ! $this->article->image)
			return $this->image;
		$this->image = new Image(DAOService::instance('ImageService')->get($this->article->image));
		return $this->image;
	}
	
	private function getGallery()
	{
		if($this->gallery != null ||  ! $this->article->gallery)
			return null;
		$this->gallery = new Gallery(DAOService::instance('PhotoGalleryService')->get($this->article->gallery));
		return $this->gallery;
	}
}

?>
