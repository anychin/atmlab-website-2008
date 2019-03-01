<?php
class Db_News_DAO extends Dao_Dao
{
	public $model = 'Db_News_News';
	
	public function __construct()
	{
		parent::__construct('news');
	}
	
	public function create($category)
	{
		return parent::insert(array('text'=>'', 'shorttext'=>'', 'title'=>'', 'pagetitle'=>'', 'keywords'=>'', 'description'=>'', 'category'=>$category, 'date'=>date('Y-m-d')));
	}
	
	public function getInCategory($category = 0, $page = -1, $itemsPerPage = -1, $fields = array(), $admin = false)
	{
		if($category == 0)
			$category = Db_News_Cat_DAO::instance()->getByUrl()->id;
		$expressions = array(Dao_Exp::eq('temp', 0), Dao_Exp::eq('category', $category));
		if( ! $admin)
			$expressions[] = Dao_Exp::eq('published', 1);
		return $this->getList($expressions, $page, $itemsPerPage, array(Dao_Order::desc('date')), $fields);
	}
	
	public function save($values)
	{
		$values['temp'] = 0;
		parent::update($values);
	}
	
	public function count($category = 0, $admin = false)
	{
		if($category == 0)
			$category = Db_News_Cat_DAO::instance()->getByUrl()->id;
		$expressions = array(Dao_Exp::eq('temp', 0), Dao_Exp::eq('category', $category));
		if( ! $admin)
			$expressions[] = Dao_Exp::eq('published', 1);
		return parent::count($expressions);
	}
	
	public function delete($id)
	{
		parent::delete($id);
	}
	
	public function publish($id)
	{
		return $this->update(array('id'=>$id, 'published'=>(1 - $this->get($id)->published)));
	}
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
}
?>