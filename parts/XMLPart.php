<?php
class XMLPart extends Part
{
	protected function doAll()
	{
		$this->output = false;
		if(count($this->url) == 0)
			throw new PageNotFoundException();
		$article = $this->articleService->getByUrl(self::escape($this->url[0]));
		if($article == null)
			throw new PageNotFoundException();
		$xml = new DOMDocument();
		$xml->substituteEntities = true;
		$root = $xml->createElement("article");
		$xml->appendChild($root);
		$root->appendChild(new DOMAttr('id', $article->id));
		$root->appendChild(new DOMAttr('name', stripslashes($article->name)));
		$root->appendChild(new DOMAttr('text', stripslashes($article->text)));
		echo $xml->saveXML();
		parent::doAll();
	}
}
?>