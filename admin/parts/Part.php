<?
require_once 'classes/Button.php';
require_once 'presets/PresetsService.php';

class Part extends BasePart
{
	public $buttons = array();
	
	protected $pageTitle = '';
	
	protected $presetsService;
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->presetsService = DAOService::instance('PresetsService');
	}
	
	public function display()
	{
		if(BaseSite::$instance->user == null && $this->partName != 'auth')
			throw new Exception();
		parent::display();
	}
	
	protected function doAll()
	{
		Template::add('currentUser', Site::$instance->user);
		Template::add('buttons', $this->buttons);
		Template::add('pageTitle', $this->pageTitle);
		Template::add('presets', $this->presetsService->getPresets());
		parent::doAll();
	}
	
	public static function navigate($address)
	{
		BasePart::navigate('/admin'.$address);
	}
	
	public static function registerInMenu()
	{
		return null;
	}
	
	public function registerButton($button)
	{
		$this->buttons[] = $button;
	}
}
?>