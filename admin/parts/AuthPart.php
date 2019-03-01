<?
class AuthPart extends Part
{
	private $userService;
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->userService = DAOService::instance('UserService');
	}
	
	protected function doAll()
	{
		if(Session::getData('user') != null)
		{
			Session::unsetData('user');
			Auth::deleteCookie();
		}
		parent::doAll();
	}
	
	public function display()
	{
		$this->templateFile = 'auth.phtml';
		parent::display();
	}
	
	public function formMethod()
	{
		$this->checkUser(parent::formMethod());
		FormController::sendData();
	}
	
	private function checkUser($data)
	{
		$login = $data[login];
		$password = md5($data[password]);
		$user = $this->userService->getByLogin($login);
		if($user == null)
		{
			FormController::addError('login', 'not.exists');
			return;
		}
		if($user->password != $password)
		{
			FormController::addError('password', 'not.valid');
			return;
		}
		if($user->isblocked && $user->login != Config::get('superUserLogin'))
		{
			FormController::addError('login', 'blocked');
			return;
		}
		if( ! $user->isInRole(UserRoles::ADMIN_ROLE) && $user->login != Config::get('superUserLogin'))
		{
			FormController::addError('login', 'not.admin');
			return;
		}
		
		Auth::storeUser($user, $data[remember]);
	}
}
?>