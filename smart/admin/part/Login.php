<?php
class Part_Login extends Part_Part
{
	public function before()
	{
		parent::before();
		$this->layout = 'auth';
	}
	
	public function form($data)
	{
		if(!$data['login'])
		{
			$this->addFormError('login', 'required');
			return;
		}
		if(!$data['password'])
		{
			$this->addFormError('password', 'required');
			return;
		}
		$login = $data['login'];
		$password = md5($data['password']);
		$user = Db_User_DAO::instance()->getByLogin($login);
		if($user == null)
		{
			$this->addFormError('login', 'not.exists');
			return;
		}
		if($user->password != $password)
		{
			$this->addFormError('password', 'not.valid');
			return;
		}
		if($user->isblocked && $user->login != Config::getInstance()->superUserLogin)
		{
			$this->addFormError('login', 'blocked');
			return;
		}
		if( ! $user->isInRole(Auth_Roles::ADMIN) && $user->login != Config::getInstance()->superUserLogin)
		{
			$this->addFormError('login', 'not.admin');
			return;
		}
		Auth_Auth::storeUser($user, $data['remember']);
	}
	
	public function logout()
	{
		Auth_Auth::removeAuth();
		self::navigate('/login');
	}
}
?>