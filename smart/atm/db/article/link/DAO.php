<?php
class Db_Article_Link_DAO extends Dao_Dao
{
	public function __construct()
	{
		parent::__construct('link');
	}
	
	public function deleteArticle($articleId)
	{
		$this->delete(0, Dao_Exp::disunction(array(Dao_Exp::eq('article', $articleId), Dao_Exp::eq('link', $articleId))));
	}
	
	public function addLink($articleId, $linkId)
	{
		return $this->insert(array('article'=>$articleId, 'link'=>$linkId));
	}
	
	public function deleteLink($articleId, $linkId)
	{
		$this->delete(0, array(Dao_Exp::eq('article', $articleId), Dao_Exp::eq('link', $linkId)));
	}
	
	public function deleteLinks($articleId)
	{
		$this->delete(0, Dao_Exp::eq('link', $articleId));
	}
	
	public function clearArticle($articleId)
	{
		$this->delete(0, Dao_Exp::eq('article', $articleId));
	}
	
	public function getLinks($articleId)
	{
		return $this->getList(Dao_Exp::eq('article', $articleId));
	}
	
	public function getOnLinks($articleId)
	{
		return $this->getList(Dao_Exp::eq('link', $articleId));
	}
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
}

?>