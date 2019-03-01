<?php
class Db_News_Cat_DAO extends Dao_Dao
{
	public $model = 'Db_News_Cat_NewsCategory';
	
	public static $defaultCategory = 'common';
	
	public function __construct()
	{
		parent::__construct('newscategory');
	}
	
	public function save($data)
	{
		$data['temp'] = 0;
		return $this->update($data);
	}
	
	public function create()
	{
		return $this->insert(array('name'=>''));
	}

	public function getByUrl($url = '')
	{
		if($url == '')
			$url = self::$defaultCategory;
		return $this->get(0, array(Dao_Exp::eq('url', $url), Dao_Exp::eq('temp', 0)));
	}
	
	public function getCategoriesList($fields = array())
	{
		return $this->getList(Dao_Exp::eq('temp', 0), -1, -1, array(Dao_Order::asc('name')), $fields);
	}
	
	public function getCategoriesShortNews($itemPerCategories = -1, $fields = array())
	{
		$catList = $this->getCategoriesList();
		for($i=0; $i<count($catList); $i++)
			$list[] = Db_News_DAO::instance()->getInCategory($catList[$i]->id, 0, $itemPerCategories, $fields);
		return $list;
	}	
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
}
?>