<?php
class LinkService extends DAOService
{
	public function __construct()
	{
		parent::__construct('link');
	}
	
	public function deleteArticle($articleId)
	{
		$this->delete(0, Expression::disunction(array(Expression::eq('article', $articleId), Expression::eq('link', $articleId))));
	}
	
	public function addLink($articleId, $linkId)
	{
		return $this->insert(array(article=>$articleId, link=>$linkId));
	}
	
	public function deleteLink($articleId, $linkId)
	{
		$this->delete(0, array(Expression::eq('article', $articleId), Expression::eq('link', $linkId)));
	}
	
	public function deleteLinks($articleId)
	{
		$this->delete(0, Exception::eq('link', $articleId));
	}
	
	public function clearArticle($articleId)
	{
		$this->delete(0, Expression::eq('article', $articleId));
	}
	
	public function getLinks($articleId)
	{
		return $this->getList(Expression::eq('article', $articleId));
	}
	
	public function getOnLinks($articleId)
	{
		return $this->getList(Expression::eq('link', $articleId));
	}
}

?>
