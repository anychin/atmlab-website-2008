<?
class SiteAuth extends Auth
{
	
	public function SiteAuth($db)
	{
		parent::__construct($db);
	}
	
	protected function checkUser($user)
	{
		$user = parent::checkUser($user);
		if($user == null)
			return null;
		if($user->login != Config::get('superUserLogin') &&  ! $user->isAdmin())
			return null;
		return $user;
	}
}
?>