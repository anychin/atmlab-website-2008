<?
require_once ('MenuItem.php');

class Site extends BaseSite
{
	protected function parseUrl()
	{
		parent::parseUrl();
		$this->url = ArrayUtil::splice($this->url, 1);
		if($this->user == null && $this->url[0] != 'auth' && $this->url[0] != 'form' && $this->url[1] != 'auth')
		{
			Part::navigate('/auth/');
			return;
		}
	}
	
	protected function resolveLanguage()
	{
		parent::resolveLanguage();
		if(Session::getData('language') != null)
			$this->currentLanguage = Session::getData('language');
	}
	
	protected function configure()
	{
		if(file_exists('language/' . $this->currentLanguage . '.php'))
			include 'language/' . $this->currentLanguage . '.php';
		else if(file_exists('language/' . Config::get('defaultLanguage') . '.php'))
			include 'language/' . Config::get('defaultLanguage') . '.php';
		parent::configure();
		if($this->form)
			return;
		$menu = array();
		foreach($this->partConfig as $part)
		{
			PartFactory::addPart($part);
			$result = call_user_func(array($part, 'registerInMenu'));
			if($result != null)
			{
				if(is_array($result))
					for($i = 0; $i < count($result); $i++)
						$menu[] = $result[$i];
				else
					$menu[] = $result;
			}
		}
		usort($menu, 'Site::sortMenu');
		Template::add('menu', $menu);
	}
	
	public static function sortMenu($n, $m)
	{
		if($n->order >= $m->order)
			return 1;
		return 0;
	}
}
?>