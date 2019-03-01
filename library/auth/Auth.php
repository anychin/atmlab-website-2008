<?
class Auth
{
	const cookieVar = 'atmsession';
	
	const sessionVar = 'user';
	
	protected $userService;
	
	protected $db;
	
	public function __construct($db)
	{
		if(Config::get('useLogin'))
		{
			$this->db = $db;
			$this->userService = DAOService::instance('UserService');
			$this->getCreditails();
			if(Site::$instance->user == null)
				$this->getRememberUser();
		}
	}
	
	public static function addCookie($login, $password)
	{
		setcookie(self::cookieVar, $login . ';' . $password, time() + 10000 * 3600, '/');
	}
	
	public static function deleteCookie()
	{
		setcookie(self::cookieVar, '', time() - 1000 * 3600);
	}
	
	protected function getCreditails()
	{
		$user = Session::getData(self::sessionVar);
		Site::$instance->user = $this->checkUser($user);
	
	}
	
	protected function getRememberUser()
	{
		if( ! isset($_COOKIE[self::cookieVar]))
			return;
		$loginPassword = split(';', $_COOKIE[self::cookieVar]);
		Site::$instance->user = $this->checkUser($this->getUserData($loginPassword[0], $loginPassword[1]));
	}
	
	protected function getUserData($login, $password)
	{
		return $this->userService->get(0, array(Expression::eq('login', $login), Expression::eq('password', $password)));
	}
	
	protected function checkUser($user)
	{
		if($user == null || ($user->isblocked && $user->login != Config::get('superUserLogin')))
			return null;
		return new User($user);
	}
	
	public static function storeUser($user, $remember = false)
	{
		Session::setData(self::sessionVar, $user->getUser());
		if($remember)
			self::addCookie($user->login, $user->password);
		else
			self::deleteCookie();
	}
	
	public static function removeAuth()
	{
		Session::unsetData(self::sessionVar);
		self::deleteCookie();
	}
}
?>