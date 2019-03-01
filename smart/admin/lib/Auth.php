<?
class Lib_Auth extends Auth_Auth
{
	
	public function __construct($db)
	{
		parent::__construct($db);
	}
	
	protected function checkUser($user)
	{
		$user = parent::checkUser($user);
		if($user == null)
			return null;
		if($user->login != Config::getInstance()->superUserLogin &&  ! $user->isAdmin())
			return null;
		return $user;
	}
}
?>