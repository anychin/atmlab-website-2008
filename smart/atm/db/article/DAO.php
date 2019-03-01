<?
class Db_Article_DAO extends Db_Tree_DAO 
{
	public $model = 'Db_Article_Article';
	
	public function __construct()
	{
		parent::__construct('articles');
	}
	
	protected function deleteFilter($id, $criteria, $table)
	{
		Db_Article_Link_DAO::instance()->deleteArticle($id);
		parent::deleteFilter($id, $criteria, $table);
	}
	
	public function getByOtherUrl($url, $admin = false)
	{
		$expressions = array(Dao_Exp::eq('otherurl', $url));
		if (!$admin)
		{
			$expressions[] = Dao_Exp::eq('temp', 0);
			$expressions[] = Dao_Exp::eq('published', 1);
		}
		return parent::get(0, $expressions);
	}

	protected function createFilter($values)
	{
		$values = parent::createFilter($values);
		$gallery = Db_Photo_Gallery_DAO::instance()->create();
		$values['otherurl'] = '';
		$values['name'] = '';
		$values['title'] = '';
		$values['descr'] = '';
		$values['text'] = '';
		$values['gallery'] = $gallery->id;
		return $values;
	}
	
	public function save($values)
	{
		if ($values['def'] == 1)
			$this->update(array('def' => 0));
		return parent::save($values);
	}

	public function getArticle($id, $admin = false, $fields = array())
	{
		return parent::getNode($id, $admin, $fields);
	}

	public function getDefault()
	{
		return parent::get(0, array(Dao_Exp::eq('temp', 0), Dao_Exp::eq('published', 1), Dao_Exp::eq('def', 1)));
	}

	public function getRootArticles($admin = false, $fields = array())
	{
		return parent::getRootNodes($admin, $fields);
	}

	public function getSubArticles($parent, $admin = false, $fields = array())
	{
		return parent::getSubNodes($parent, $admin, $fields);
	}
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
}
?>