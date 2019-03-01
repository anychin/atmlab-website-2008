<?
class Lib_Site extends BaseSite
{
	
	protected function additionalProcessResult()
	{
		$menu = array();
		foreach($config = Config::getInstance()->menu as $item)
		{
			$cl = ucfirst($item);
			if(!file_exists('part/'.$cl.'.php'))
				continue;
			$result = call_user_func(array('Part_'.$cl, 'registerInMenu'));
			if($result)
				$menu[] = $result;
		}
		usort($menu, array("self", "sortMenu"));
		$this->view->menu = $menu;
	}
	
	public static function sortMenu($n, $m)
	{
		if($n->order >= $m->order)
			return 1;
		return 0;
	}
}
?>