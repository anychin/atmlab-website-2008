<?php
class Db_User_DAO extends Dao_Dao
{
	private $anketaDAO;
	
	public $model = 'Db_User_User';
	
	public function __construct()
	{
		parent::__construct('users');
		$this->anketaDAO = Db_User_AnketaDAO::instance();
	}
	
	public function getByLogin($login)
	{
		return $this->get(0, Dao_Exp::eq('login', $login));
	}
	
	public function getByMail($email)
	{
		return $this->get(0, Dao_Exp::eq('email', $email));
	}
	
	public function getByLoginAndPassword($login, $password)
	{
		return $this->get(0, array(Dao_Exp::eq('login', $login), Dao_Exp::eq('password', $password)));
	}
	
	public function register($userValues, $anketaValues, $roles)
	{
		$userValues['isblocked'] = 0;
		if(Config::get('blockAfterRegistration'))
			$userValues['isblocked'] = 1;
		$userValues['roles'] = join(',', $roles);
		$anketa = $this->anketaDAO->register($anketaValues);
		$userValues['anketa'] = $anketa->id;
		return $this->insert($userValues);
	}
	
	public function edit($userValues, $anketaValues, $roles = null)
	{
		$this->anketaDAO->saveAnketa($anketaValues);
		$userValues['roles'] = join(',', $roles);
		return $this->update($userValues);
	}
	
	public function delete($id)
	{
		$user = $this->get($id);
		parent::delete($id);
		$this->anketaDAO->delete($user->anketa);
	}
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
}
?>