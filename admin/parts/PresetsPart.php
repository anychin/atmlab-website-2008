<?
class PresetsPart extends Part
{
	function __construct($name)
	{
		parent::__construct($name);
	}

	public function doAll()
	{
		if(count($this->url) == 0)
		{
			$this->title = 'Настройки';
			$this->pageTitle = 'Общие настройки сайта';
			$this->registerButton(new Button('save', '', Dictionary::get('save')));
		}
		else if($this->url[0] == 'cache')
		{
			$cache = new Cache();
			$cache->clean();
			file_put_contents('test', Config::get('cacheDir'));
		}
		parent::doAll();
	}

	public function formMethod()
	{
		$data = parent::formMethod();
		$this->presetsService->update($data);
		FormController::sendData();
	}
	
	public static  function registerInMenu()
	{
		return new MenuItem('Настройки', '/presets',4);
	}
}
?>