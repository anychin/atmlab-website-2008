<?php
class Db_Photo_Photo
{
	private $img = null;
	
	private $gal = null;
	
	public function __get($field)
	{
		if($field == 'gal')
			return $this->getGallery();
		if($field == 'img')
			return $this->getImage();
		if(!isset($this->$field))
			return null;
		return $this->$field;
	}
	
	protected function getImage()
	{
		if($this->img != null ||  ! $this->image)
			return $this->img;
		$this->img = Db_Image_DAO::instance()->get($this->image);
		return $this->img;
	}
	
	protected function getGallery()
	{
		if($this->gal != null || !$this->gallery)
			return $this->gal;
		$this->gal = Db_Photo_Gallery_DAO::instance()->get($this->gallery);
		return $this->gal;
	}
	
	public function serialize()
	{
		$photo = clone $this;
		$photo->image = clone $this->getImage();
		$photo->image->resize = $this->img->resizes;
		return json_encode($photo); 
	}
}
?>