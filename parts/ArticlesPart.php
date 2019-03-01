<?php
class ArticlesPart extends Part
{
	protected function doAll()
	{
		if(count($this->url) == 0)
		{
			parent::doAll();
			return;
		}
		$article = $this->articleService->getByUrl(self::escape($this->url[0]));
		if($article == null)
			throw new PageNotFoundException();
		$this->title = stripslashes($article->title);
		$this->keywords = stripslashes($article->metakeys);
		$this->description = stripslashes($article->descr);
		Template::add('article', $article);
		parent::doAll();
	}
}
?>