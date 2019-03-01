<?php
class RealImageResize
{
	private $resize = null;
	
	public function __construct($resize)
	{
		$this->resize = $resize;
	}
	
	public function serialize()
	{
		$xml = new DOMDocument();
		$this->getDom($xml);
		return $xml->saveXML();
	}
	
	public function getDom($xml)
	{
		$root = $xml->createElement('image');
		$xml->appendChild($root);
		$root->appendChild(new DOMAttr('id', $this->resize->id));
		$root->appendChild(new DOMAttr('width', $this->resize->width));
		$root->appendChild(new DOMAttr('height', $this->resize->height));
		$root->appendChild(new DOMAttr('name', $this->resize->name));
	}
}
?>
