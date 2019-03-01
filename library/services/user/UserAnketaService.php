<?php
class UserAnketaService extends DAOService
{
	public function __construct()
	{
		parent::__construct('anketa');
	}
	
	public function register($values)
	{
		return $this->insert($values);
	}
	
	public function saveAnketa($values)
	{
		return $this->update($values);
	}
}
?>