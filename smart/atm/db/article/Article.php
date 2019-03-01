<?php
class Db_Article_Article
{
	private $img = null;
	
	private $gal = null;
	
	public function __get($field)
	{
		if($field == 'img')
			return $this->getImage();
		if($field == 'gal')
			return $this->getGallery();
		if(isset($this->$field))
			return $this->$field;
		return null;
	}
	
	private function getImage()
	{
		if($this->img != null ||  ! $this->image)
			return $this->img;
		$this->img = Db_Image_DAO::instance()->get($this->image);
		return $this->img;
	}
	
	private function getGallery()
	{
	}
}
?>