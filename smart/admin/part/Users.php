<?php
class Part_Users extends Part_Part
{
	private $dao;
	
	public function __construct()
	{
		parent::__construct();
		$this->dao = Db_User_DAO::instance();
		$this->title = $this->pageTitle = "Управление пользователями";
	}
	
	public function index()
	{
		$this->view->users = $this->dao->getList(null, -1, -1, array(Dao_Order::asc("login")));
	}
	
	public function edit()
	{
		$this->view->user = $this->dao->get($this->route['id']);
		$this->registerButton(new Lib_Button("save", "", 'Сохранить'));
	}
	
	public function form($data)
	{
		$user = $this->dao->get($data['id']);
		if($user->email != $data['email'] && !$this->checkEMail($data)){
			$this->addFormError("email", "not.unique");
			return;
		}
		$userData = array('id'=>$data['id'], 'email'=>$data['email'], 'isblocked'=>$data['isblocked']);
		if($data['password'] != '')
			$userData['password'] = md5(trim($data['password']));
		$this->dao->update($userData);
	}
	
	public function delete()
	{
		$this->dao->delete($this->route['id']);
		$this->output = false;
	}
	
	public function publish()
	{
		$id = $this->route['id'];
		$user = $this->dao->get($id);
		$this->output = false;
		if($user->isblocked == 0)
			$userData = array('id'=>$id, 'isblocked'=>1);
		else
			$userData = array('id'=>$id, 'isblocked'=>0);
		$this->dao->update($userData);
	}	
	
	private function checkEmail($data)
	{
		$user = $this->dao->getByMail($data['email']);
		if($user != null)
			return false;
		return true;
	}
	
	public static  function registerInMenu()
	{
		return new Lib_MenuItem("Пользователи", "/users", 3);
	}
	
}
?>