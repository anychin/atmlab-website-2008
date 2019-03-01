<?php
class Db_Presets_DAO extends Dao_Dao
{
	public function __construct()
	{
		parent::__construct("common");
	}
	
	public function getPresets()
	{
		return $this->get(1);
	}
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
}
?>