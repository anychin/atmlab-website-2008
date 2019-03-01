<?php
class Db_Image_Resize_DAO extends Dao_Dao
{
	public function __construct()
	{
		parent::__construct('imageresize');
	}
	
	public function getResizes($imageId)
	{
		return $this->getList(Dao_Exp::eq('image', $imageId), -1, -1, array(Dao_Order::desc('name')));
	}
	
	public function create($imageId)
	{
		return $this->insert(array('image'=>$imageId));
	}
	
	public function getResize($imageId, $name)
	{
		return $this->get(0, array(Dao_Exp::eq('image', $imageId), Dao_Exp::eq('name', $name)));
	}
	
	public function deleteResizes($imageId)
	{
		$this->delete(0, Dao_exp::eq('image', $imageId));
	}
	
	public function deleteResize($id)
	{
		$this->delete($id);
	}
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
}
?>