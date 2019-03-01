<?php
class Db_News_News
{
	private $img = null;
	
	private $gal = null;
	
	public function __get($field)
	{
		if($field == 'img')
			return $this->getImage();
		if($field == 'gal')
			return $this->getGallery();
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
		if($this->gal != null || ! $this->gallery)
			return $this->gal;
		$this->gal = Db_Photo_Gallery_DAO::instance()->get($this->gallery);
		return $this->gal;
	}
}
?>