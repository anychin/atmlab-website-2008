<?php
require_once ('UserAnketaService.php');
require_once ('RoleService.php');
require_once ('User.php');

class UserService extends DAOService
{
	private $anketaService;
	
	private $roleService;
	
	public function __construct()
	{
		parent::__construct('users');
		$this->anketaService = DAOService::instance('UserAnketaService');
		$this->roleService = DAOService::instance('RoleService');
	}
	
	public function register($userValues, $anketaValues, $roles)
	{
		if(Config::get('blockAfterRegistration'))
			$userValues[isblocked] = 1;
		else
			$userValues[isblocked] = 0;
		$anketa = $this->anketaService->register($anketaValues);
		$userValues[anketa] = $anketa->id;
		$user = $this->insert($userValues);
		for($i = 0; $i < count($roles); $i++)
		{
			$role = $this->roleService->getByName($roles[$i]);
			$this->roleService->addRole($user->id, $role->id);
		}
		return new User($user);
	}
	
	public function getByLogin($login)
	{
		$user = $this->get(0, Expression::eq('login', $login));
		if($user == null)
			return $user;
		else
			return new User($user);
	}
	
	public function getByMail($email)
	{
		$user = $this->get(0, Expression::eq('email', $email));
		if($user == null)
			return $user;
		else
			return new User($user);
	}
	
	public function edit($userValues, $anketaValues, $roles)
	{
		$this->anketaService->saveAnketa($anketaValues);
		$user = $this->update($userValues);
		$existRoles = $this->roleService->getByUser($user->id);
		$existsRolesIds = array();
		for($i = 0; $i < count($existRoles); $i++)
			if( ! in_array($existRoles[$i]->id, $roles))
			{
				$this->roleService->deleteRole($user->id, $existRoles[$i]->id);
			}
			else
				$existsRolesIds[] = $existRoles->id;
		for($i = 0; $i < count($roles); $i++)
			if( ! in_array($roles[$i], $existsRolesIds))
				$this->roleService->addRole($user->id, $roles[$i]);
		return new User($user);
	}
	
	public function delete($id)
	{
		$user = $this->get($id);
		parent::delete($id);
		$this->roleService->deleteUserRoles($id);
		$this->anketaService->delete($user->anketa);
	}
}
?>