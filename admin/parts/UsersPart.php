<?php
class UsersPart extends Part
{
	private $service;
	
	private $anketService;
	
	private $roleService;
	
	private $itemsPerPage = 20;
	
	public function __construct($name, $db)
	{
		parent::__construct($name, $db);
		$this->service = new UserService($this->db);
		$this->anketService = new UserAnketaService($this->db);
		$this->roleService = new RoleService($this->db);
	}
	
	public function doAll()
	{
		$this->title = "Управление пользователями";
		$this->parseUrl();
		parent::doAll();
	}
	
	private function parseUrl()
	{
		if(count($this->url) > 0 && $this->url[0] == "roles")
		{
			$this->url = ArrayUtil::splice($this->url, 1);
			$this->parseRoleUrl();
			Template::add("roleTemplate", true);
			$this->pageTitle = "Управление ролями пользователей";
		}
		else
		{
			$this->pageTitle = "Управление пользователями";
			$this->parseUserUrl();
		}
	}
	
	private function parseRoleUrl()
	{
		if(count($this->url) == 0)
		{
			Template::add("roles", $this->roleService->getList());
			self::registerButton(new Button("new", "/admin/users/roles/new", Dictionary::get("role.new")));
			return;
		}
		if($this->url[0] == "edit")
		{
			Template::add("role", $this->roleService->get($this->url[1]));
			Template::add("form", true);
			$this->registerButton(new Button("save", "", Dictionary::get("save")));
			return;
		}
		if($this->url[0] == "delete")
		{
			$this->roleService->delete($this->url[1]);
			return;
		}
		if($this->url[0] == "new")
		{
			$role = $this->roleService->insert(array());
			self::navigate("/users/roles/edit/".$role->id);
			return;
		}
	}
	
	private function parseUserUrl()
	{
		if(count($this->url) == 0 || $this->url[0] == "page")
		{
			$page = 0;
			if($this->url[0] == "page")
				$page = $this->url[1] - 1;
			if($page < 0)
				$page = 0;
			Template::add("users", $this->service->getList(null, $page, $this->itemsPerPage, array(Order::asc("login"))));
			Template::add("pages", ceil($this->service->count() / $this->itemsPerPage));
			Template::add("page", $page + 1);
			return;
		}
		$operation = $this->url[0];
		if ($operation == "edit")
		{
			Template::add("form", true);
			Template::add("user", new User($this->service->get($this->url[1])));
			Template::add("roles", $this->roleService->getList());
			$this->registerButton(new Button("save", "", Dictionary::get("save")));
			return;
		}
		if ($operation == "delete")
		{
			$this->service->delete($this->url[1]);
			$this->output = false;
			return;
		}
		if($operation == "block")
		{
			$user = $this->service->get($this->url[1]);
			$this->output = false;
			if($user->isblocked == 0)
				$userData = array(id=>$this->url[1], isblocked=>1);
			else
				$userData = array(id=>$this->url[1], isblocked=>0);
			$this->service->update($userData);
			return;
		}
	}
	
	public function formMethod()
	{
		$data = parent::formMethod();
		if(count($this->url) > 0)
			$this->updateRole($data);
		else
			$this->updateUser($data);
		FormController::sendData();
	}
	
	private function updateRole($data){
		$role = $this->roleService->get($data[id]);
		$roleByName = $this->roleService->getByName($data[name]);
		if($roleByName != null && $data[name] != $role->name)
			FormController::addError("name", "not.unique");
		else
			$this->roleService->save($data);
	}
	
	private function updateUser($data)
	{
		$user = $this->service->get($data[id], true);
		if($user->email != $data[email] && !$this->checkEMail($data)){
			FormController::addError("email", "not.unique");
			return;
		}
		if($data[password] != $user->password && $data[password] != "")
			$userData = array(id=>$data[id], password=>md5($data[password]), email=>$data[email], isblocked=>$data[isblocked]);
		else
			$userData = array(id=>$data[id], email=>$data[email], isblocked=>$data[isblocked]);
		$anketaData = array(id=>$user->anketa);
		$roles = explode(",", $data[roles]);
		if(FormController::isValid())
			$this->service->edit($userData, $anketaData, $roles);
	}	
	
	private function checkEmail($data)
	{
		$user = $this->service->getByMail($data[email]);
		if($user != null)
			return false;
		return true;
	}
	
	public static  function registerInMenu()
	{
		return new MenuItem("Пользователи", "", 3, array(
			new MenuItem("Пользователи", "/users"),
			new MenuItem("Роли пользователей", "/users/roles")
		));
	}
}
?>