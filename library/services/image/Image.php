<?php
class Image
{
	private $image = null;
	
	private $resizes = null;
	
	public function __construct($image)
	{
		$this->image = $image;
	}
	
	public function __get($field)
	{
		if($field == 'resizes')
			return $this->getResizes();
		return $this->image->$field;
	}
	
	private function getResizes()
	{
		if($this->resizes != null)
			return $this->resizes;
		$resizes = DAOService::instance('ImageService')->getResizes($this->image->id);
		$this->resizes = array();
		for($i = 0; $i < count($resizes); $i++)
			$this->resizes[$resizes[$i]->name] = $resizes[$i];
		return $this->resizes;
	}
	
	public function serialize()
	{
		$xml = new DOMDocument();
		$this->getDom($xml);
		return $xml->saveXML();
	}
	
	public function getDom($xml, $parentNode = null)
	{
		$root = $xml->createElement('image');
		if($parentNode == null)
			$xml->appendChild($root);
		else
			$parentNode->appendChild($root);
		$attr = $xml->createAttribute('id');
		$attr->value = $this->image->id;
		$root->appendChild($attr);
		$attr = $xml->createAttribute('width');
		$attr->value = $this->image->width;
		$root->appendChild($attr);
		$attr = $xml->createAttribute('height');
		$attr->value = $this->image->height;
		$root->appendChild($attr);
		$resizes = $xml->createElement('resizes');
		$this->getResizes();
		foreach($this->resizes as $name=>$r)
		{
			$resize = $xml->createElement('resize');
			$attr = $xml->createAttribute('id');
			$attr->value = $r->id;
			$resize->appendChild($attr);
			$attr = $xml->createAttribute('width');
			$attr->value = $r->width;
			$resize->appendChild($attr);
			$attr = $xml->createAttribute('height');
			$attr->value = $r->height;
			$resize->appendChild($attr);
			$attr = $xml->createAttribute('name');
			$attr->value = $name;
			$resize->appendChild($attr);
			$resizes->appendChild($resize);
		}
		$root->appendChild($resizes);
	}
}

?>
