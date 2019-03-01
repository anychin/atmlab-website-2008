<?php
class Part_Articles extends Part_Part
{
	public function get()
	{
		$url = self::escape($this->route['url']);
		$article = $this->articleDao->getByUrl($url);
		if($article == null)
			throw new Part_PageNotFoundException();
		$this->view->article = $article;
	}
	
	public function after()
	{
		if($this->view->article)
		{
			$this->title = $this->view->article->title;
			$this->keywords = $this->view->article->metakeys;
			$this->description = $this->view->article->descr;
		}
		parent::after();
	}
	
	public function robots()
	{
		header("Content-Type:text/plain; charset=UTF-8");
		$s = BaseSite::$instance->serverName;
		if(strpos($s, 'www') !== false)
			$s = str_replace('www.', '', $s);
		$this->view->s = $s;
		$this->layout = "robots";
	}
}
?>