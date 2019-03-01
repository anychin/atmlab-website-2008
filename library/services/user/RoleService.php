<?php
class RoleService extends DAOService
{
	public function __construct()
	{
		parent::__construct('roles');
	}
	
	public function save($data)
	{
		$data[temp] = 0;
		return $this->update($data);
	}
	
	public function getList()
	{
		return parent::getList(Expression::eq('temp', 0));
	}
	
	public function getByUser($userId)
	{
		
		return parent::getList(array(Expression::eq('r.temp', 0), Expression::eq('ur.user', intval($userId)), Expression::query('r.id = ur.role')),  - 1,  - 1, null, array('r.*'), 'roles r, userrole ur');
	}
	
	public function getByName($roleName)
	{
		return parent::getList(array(Expression::eq('temp', 0), Expression::eq('name', $roleName)));
	}
	
	public function addRole($userId, $roleId)
	{
		$role = $this->get(0, array(Expression::eq('user', $userId), Expression::eq('role', $roleId)), array(), 'userroles');
		if($role == null)
			$this->insert(array(user=>$userId, role=>$roleId), 'userroles');
	}
	
	public function deleteRole($userId, $roleId)
	{
		parent::delete(0, array(Expression::eq('user', $userId), Expression::eq('role', $roleId)), 'userroles');
	}
	
	public function delete($roleId)
	{
		parent::delete(0, array(Expression::eq('role', $roleId)), 'userroles');
		parent::delete($roleId);
	}
	
	public function deleteUserRoles($userId)
	{
		parent::delete(0, array(Expression::eq('user', $userId)), 'userroles');
	}
}
?>