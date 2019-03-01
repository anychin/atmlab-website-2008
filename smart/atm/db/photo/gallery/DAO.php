<?php
class Db_Photo_Gallery_DAO extends Dao_Dao 
{
	public $model = 'Db_Photo_Gallery_Gallery';
	
	public function __construct()
	{
		parent::__construct('photogallery');
	}
	
	public function getByUrl($url)
	{
		return $this->get(0, Dao_Exp::eq("url",$url));
	}
	
	public function create()
	{
		return $this->insert(array('description'=>"", 'title'=>"Новая фотогаллерея"));
	}
	
	public function delete($id)
	{
		Db_Photo_DAO::instance()->deleteInGallery($id);
		parent::delete($id);
	}
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
}
?>