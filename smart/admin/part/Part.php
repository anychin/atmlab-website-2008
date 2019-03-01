<?
class Part_Part extends Part_BasePart
{
	protected $buttons = array();
	
	protected $pageTitle = '';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function before()
	{
		if(get_class($this) != 'Part_Login' && (BaseSite::$instance->user == null || !BaseSite::$instance->user->isAdmin()))
			throw new Auth_AccessDeniedException();
		$this->layout = 'main';
		parent::before();
	}
	
	public function after()
	{
		if($this->output)
		{
			$this->view->buttons = $this->buttons;
			$this->view->pageTitle = $this->pageTitle;
			$this->view->presets = Db_Presets_DAO::instance()->getPresets();
		}
		parent::after();
	}
	
	public static function navigate($address)
	{
		parent::navigate('/admin'.$address);
	}
	
	public static function registerInMenu()
	{
		return null;
	}
	 
	protected function registerButton($button)
	{
		$this->buttons[] = $button;
	}
}
?>