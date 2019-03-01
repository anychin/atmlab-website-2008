<?php
class Db_User_User
{
	private $rolesObj = null;
	
	private $anketaObj = null;
	
	private $imageObj = null;
	
	public function __get($field)
	{
		if($field == 'a')
			return $this->getAnketa();
		if($field == 'r')
			return $this->getRoles();
		if($field == 'image')
			return $this->getImage();
		if(!isset($this->$field))
			return null;
		return $this->$field;
	}
	
	private function getAnketa()
	{
		if($this->anketaObj == null)
			Db_User_AnketaDAO::instance()->get($this->anketa);
		return $this->anketaObj;
	}
	
	private function getRoles()
	{
		if($this->rolesObj == null)
			$this->rolesObj = explode(',', $this->roles);
		return $this->rolesObj;
	}
	
	private function getImage()
	{
		
	}
	
	public function isAdmin()
	{
		return $this->isInRole(Auth_Roles::ADMIN);
	}
	
	public function isSuperAdmin()
	{
		return $this->isInRole(Auth_Roles::SUPER_USER);
	}
	
	public function isInRole($role)
	{
		return in_array($role, $this->getRoles());
	}
}
?>