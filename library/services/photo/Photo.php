<?php
class Photo
{
	private $photo = null;
	
	private $image = null;
	
	private $gallery = null;
	
	public function __construct($photo)
	{
		$this->photo = $photo;
	}
	
	public function __get($field)
	{
		if($field != 'gallery' && $field != 'image')
			return $this->photo->$field;
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
		if($this->image != null ||  ! $this->photo->image)
			return $this->image;
		$this->image = new Image(DAOService::instance('ImageService')->get($this->photo->image));
		return $this->image;
	}
	
	protected function getGallery()
	{
		if($this->gallery != null)
			return;
		$this->gallery = DAOService::instance('PhotoGalleryService')->get($this->photo->gallery);
	}
	
	public function serialize()
	{
		$this->getImage();
		$xml = new DOMDocument();
		$root = $xml->createElement('photo');
		$xml->appendChild($root);
		$attr = $xml->createAttribute('id');
		$attr->value = $this->photo->id;
		$root->appendChild($attr);
		$this->image->getDom($xml, $root);
		return $xml->saveXML();
	}
}

?>
