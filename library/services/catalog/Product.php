<?php

class Product
{
	private $product = null;
	
	private $image = null;
	
	private $description = null;
	
	private $gallery = null;
	
	private $category = null;
	
	public function __construct($product)
	{
		$this->product = $product;
	}
	
	public function __get($field)
	{
		if($field == 'image')
			return $this->getImage();
		if($field == 'advanced')
			return $this->getDescription();
		if($field == 'gallery')
			return $this->getGallery();
		if($field == 'cat')
			return $this->getCategory();
		return $this->product->$field;
	}
	
	private function getImage()
	{
		if($this->image != null ||  ! $this->product->image)
			return $this->image;
		$this->image = new Image(DAOService::instance('ImageService')->get($this->product->image));
		return $this->image;
	}
	
	private function getDescription()
	{
		if($this->description != null)
			return $this->description;
		$this->description = DAOService::instance('CatalogDescriptionService')->get($this->product->advanced);
		return $this->description;
	}
	
	private function getGallery()
	{
		if($this->gallery != null)
			return $this->gallery;
		$this->gallery = DAOService::instance('PhotoService')->getByGallery($this->product->gallery);
		return $this->gallery;
	}
	
	private function getCategory()
	{
		if($this->category != null)
			return $this->category;
		$this->category = DAOService::instance('CatalogCategoryService')->get($this->product->category);
		return $this->category;
	}
}
?>
