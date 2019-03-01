<?php
class Project
{
	private $project;
	
	private $image = null;
	
	private $gallery = null;
	
	private $category = null;
	
	public function __construct($project)
	{
		$this->project = $project;
	}
	
	public function __get($field)
	{
		if($field == 'image')
			return $this->getImage();
		if($field == 'gal')
			return $this->getGallery();
		if($field == 'cat')
			return $this->getCategory();
		return $this->project->$field;
	}
	
	private function getImage()
	{
		if($this->image != null ||  ! $this->project->image)
			return $this->image;
		$this->image = new Image(DAOService::instance('ImageService')->get($this->project->image));
		return $this->image;
	}
	
	private function getGallery()
	{
		if($this->gallery != null ||  ! $this->project->gallery)
			return null;
		$this->gallery = new Gallery(DAOService::instance('PhotoGalleryService')->get($this->project->gallery));
		return $this->gallery;
	}
	
	private function getCategory()
	{
		if($this->category != null)
			return $this->category;
		$this->category = DAOService::instance('ProjectsCategoryService')->get($this->project->category);
		return $this->category;
	}
}
?>