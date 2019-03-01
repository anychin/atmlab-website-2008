<?php
class Db_User_AnketaDAO extends Dao_Dao
{
	public function __construct()
	{
		parent::__construct('anketa');
	}
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
	
	public function register($values)
	{
		return $this->insert($values);
	}
	
	public function saveAnketa($values)
	{
		return $this->update($values);
	}
	
	public function getByUser($userId)
	{
		return $this->get(0, Dao_Exp::eq('user', $userId));
	}
}
?>