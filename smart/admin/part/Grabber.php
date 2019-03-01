<?php
class Part_Grabber extends Part_Part
{
	private $dao;
	
	public function __construct()
	{
		parent::__construct();
		$this->dao = new Dao_Dao('grabber');
		$this->title = $this->pageTitle = "Грабберы";
	}
	
	public function index()
	{
		$this->view->grabbers = $this->dao->getList();
		$this->registerButton(new Lib_Button("new", "/grabber/add", 'Добавить'));
	}
	
	public function edit()
	{
		$this->view->grabber = $this->dao->get($this->route['id']);
		$this->registerButton(new Lib_Button("save", "", 'Сохранить'));
		$this->partial = "form";
	}
	
	public function add()
	{
		$this->registerButton(new Lib_Button("save", "", 'Сохранить'));
		$this->partial = "form";
	}
	
	public function form($data)
	{
		if(isset($data['id']))
			$item = $this->dao->update($data);
		else
			$item = $this->dao->insert($data);
	}
	
	public static  function registerInMenu()
	{
		return new Lib_MenuItem("Грабберы", "/grabber", 3);
	}
	
}
?>