<?
class Auth_Auth
{
	const cookieVar = 'atmsession';
	
	const sessionVar = 'user';
	
	protected $userDAO;
	
	protected $db;
	
	public function __construct($db)
	{
		if(Config::getInstance()->useLogin)
		{
			$this->db = $db;
			$this->userDAO = Db_User_DAO::instance();
			$this->getCreditails();
			if(BaseSite::$instance->user == null)
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
		$user = Session_Session::getData(self::sessionVar);
		if($user != null)
			$user = $this->getUserData($user->login, $user->password);
		BaseSite::$instance->user = $this->checkUser($user);
	
	}
	
	protected function getRememberUser()
	{
		if( ! isset($_COOKIE[self::cookieVar]))
			return;
		$loginPassword = split(';', $_COOKIE[self::cookieVar]);
		BaseSite::$instance->user = $this->checkUser($this->getUserData($loginPassword[0], $loginPassword[1]));
		Session_Session::setData(self::sessionVar, BaseSite::$instance->user);
	}
	
	protected function getUserData($login, $password)
	{
		return $this->userDAO->getByLoginAndPassword($login, $password);
	}
	
	protected function checkUser($user)
	{
		if($user == null || ($user->isblocked && $user->login != Config::getInstance()->superUserLogin))
			return null;
		return $user;
	}
	
	public static function storeUser($user, $remember = false)
	{
		Session_Session::setData(self::sessionVar, $user);
		if($remember)
			self::addCookie($user->login, $user->password);
		else
			self::deleteCookie();
	}
	
	public static function removeAuth()
	{
		Session_Session::unsetData(self::sessionVar);
		self::deleteCookie();
	}
}
?>