<?php
class User
{
	private $user = null;
	
	private $roles = null;
	
	private $anketa = null;
	
	public function __construct($user)
	{
		$this->user = $user;
	}
	
	public function __get($field)
	{
		if($field != 'anketa' && $field != 'roles')
			return $this->user->$field;
		if($field == 'anketa')
		{
			$this->getAnketa();
			return $this->anketa;
		}
		if($field == 'roles')
		{
			$this->getRoles();
			return $this->roles;
		}
		return null;
	}
	
	private function getAnketa()
	{
		if($this->anketa == null)
			$this->anketa = DAOService::instance('UserAnketaService')->get($this->user->anketa);
	}
	
	public function getRoles()
	{
		if($this->roles == null)
			$this->roles = DAOService::instance('RoleService')->getByUser($this->user->id);
	}
	
	public function isAdmin()
	{
		$this->getRoles();
		return $this->isInRole(UserRoles::ADMIN_ROLE);
	}
	
	public function isSuperAdmin()
	{
		$this->getRoles();
		return $this->isInRole(UserRoles::SUPER_ROLE);
	}
	
	public function isInRole($roles)
	{
		$this->getRoles();
		if(is_array($roles))
		{
			for($i = 0; $i < count($this->roles); $i++)
				if(in_array($this->roles[$i], $roles))
					return true;
		}
		else
		{
			for($i = 0; $i < count($this->roles); $i++)
				if($this->roles[$i]->name == $roles)
					return true;
		}
		return false;
	}
	
	public function getUser()
	{
		return $this->user;
	}
}
?>