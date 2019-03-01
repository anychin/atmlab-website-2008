<?php
class Db_Photo_Gallery_Gallery
{
	private $photos = null;
	
	public function __get($field)
	{
		if($field == 'photos')
			return $this->getPhotos();
		if(!isset($this->$field))
			return null;
		return $this->$field;
	}
	
	protected function getPhotos()
	{
		if($this->photos != null)
			return $this->photos;
		$this->photos = Db_Photo_DAO::instance()->getByGallery($this->id);
		return $this->photos;
	}
}

?>
