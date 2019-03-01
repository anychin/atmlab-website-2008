<?php
class NewsPart extends Part
{
	private $itemsPerPage = 5;
	
	protected function doAll()
	{
		$this->parseUrl();
		parent::doAll();
	}
	
	private function parseUrl()
	{
		if($this->url[0] == 'xml')
		{
			$this->output = false;
			$this->getXML();
			return;
		}
		$page = 0;
		if(count($this->url) == 0 || $this->url[0] == 'page')
		{
			if($this->url[0] == 'page' && count($this->url) == 2)
			{
				$page = intval($this->url[1]);
				if($page > 0)
					$page--;
			}
			$count = $this->newsService->count('common');
			$count = ceil($count / $this->itemsPerPage);
			Template::add('pages', $count);
			if($page >= $count)
				$page = 0;
			Template::add('news', $this->newsService->getInCategory('common', $page, $this->itemsPerPage));
			Template::add('page', $page + 1);
			return;
		}
		$news = $this->newsService->get(self::escape($this->url[0]));
		if($news == null)
			throw new PageNotFoundException();
		Template::add('item', true);
		$this->title .= ' | ' . stripslashes($news->title);
		if($news->pageTitle != '')
			$this->title = stripslashes($news->pageTitle);
		if($news->keywords != '')
			$this->keywords = stripslashes($news->keywords);
		if($news->description != '')
			$this->description = $news->description;
		Template::add('news', $news);
	}
	
	private function getXML()
	{
		$position = intval($_GET['position']);
		$cat = $this->newsCategoryService->getByUrl('');
		$news = DAOService::$bd->getData('select id, shorttext, title from ' . DB_PREFIX . 'news where category = ' . $cat->id . ' and temp = 0 and published = 1 order by date desc limit ' . $position . ', 3');
		$xml = new DOMDocument();
		$root = $xml->createElement('news');
		$xml->appendChild($root);
		for($i = 0; $i < count($news); $i++)
		{
			$el = $xml->createElement('news');
			$root->appendChild($el);
			$el->appendChild(new DOMAttr('id', $news[$i]->id));
			$el->appendChild(new DOMAttr('text', stripslashes($news[$i]->shorttext)));
			$el->appendChild(new DOMAttr('title', stripslashes($news[$i]->title))); 
		}
		echo $xml->saveXML();
	}

}
?>