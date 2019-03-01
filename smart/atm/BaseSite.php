<?
class BaseSite
{

	public $route;

	public $currentPart;

	public $db;

	public $serverName;

	public $auth;

	public static $instance;

	public $user = null;
	
	protected $view = null;

	public function __construct ()
	{
		self::$instance = $this;
		Session_Session::start();
		$this->db = new DB_MySQL();
		Dao_Dao::$db = $this->db;
		$this->serverName = $_SERVER['SERVER_NAME'];
		Config::getInstance()->server = $this->serverName;
		if (! Config::getInstance()->authClass)
			$this->auth = new Auth_Auth($this->db);
		else {
			$cl = Config::getInstance()->authClass;
			$this->auth = new $cl($this->db);
		}
		if (Config::getInstance()->initDB)
			$this->initDataBase();
		$this->route = Route_Router::match();
	}

	public function go ()
	{
		try {
			$this->doAll();
		} catch (Exception $e) {
			if ($e instanceof Part_PageNotFoundException) {
				header("HTTP/1.0 404 Not Found");
				$this->renderOtherPart('Error', 'error');
			}
			if ($e instanceof Auth_AccessDeniedException) {
				Part_Part::navigate('/login');
			}
		}
	}
	
	protected function renderOtherPart($name, $action = 'index')
	{
		$this->instantiatePart($name, $action);
	}

	protected function doAll ()
	{
		$config = Config::getInstance();
		$part = $this->getPartName($this->route['part']);
		$action = $this->route['action'];
		if (! file_exists('part/' . $part.'.php')) {
			$part = ucfirst($config->defaultPart);
			$action = 'index';
		}
		$this->instantiatePart($part, $action);
	}
	
	protected function getPartName($url)
	{
		$parts = explode('-', $url);
		foreach($parts as $i => $part)
			$parts[$i] = ucfirst($part);
		return join('', $parts);
	}
	
	protected function getActionName($url)
	{
		$parts = explode('-', $url);
		foreach($parts as $i => $part)
		{
			if($i == 0)
				continue;
			$parts[$i] = ucfirst($part);
		}
		return join('', $parts);
	}
	
	protected function instantiatePart($name, $action)
	{
		$this->route['action'] = $action;
		$this->route['part'] = strtolower($name);
		if (! Loader::checkClassExistance('Part_'.$name))
			throw new Part_PageNotFoundException('Part Not Found');
		$name = 'Part_' . $name;
		$this->currentPart = new $name();
		$methodName = $this->getActionName($action);
		if(!method_exists($this->currentPart, $methodName))
			throw new Part_PageNotFoundException('Action Not Found');
		$this->currentPart->route = $this->route;
		$this->view = View_View::getInstance();
		$this->currentPart->view = $this->view;
		$this->currentPart->before();
		if(isset($this->currentPart->route['stop']) && $this->currentPart->route['stop'])
			return;
		if($methodName == 'form')
		{
			$this->currentPart->output = false;
			$this->currentPart->$methodName($this->filterFormData());
		}
		else
		{
			$this->currentPart->$methodName();
		}
		$this->currentPart->after();
		if($methodName == 'form')
			$this->processFormResult();
		else
			$this->processResult();
	}
	
	protected function processResult()
	{
		if(!$this->currentPart->output)
			return;
		$this->view->layout = $this->currentPart->layout;
		if($this->currentPart->template != null && strstr($this->currentPart->template, '/') === false)
			$this->currentPart->template = $this->route['part'] . '/' . $this->currentPart->template;
		if($this->currentPart->partial == null)
			$this->currentPart->partial = $this->route['action'];
		if(strstr($this->currentPart->partial, '/') === false)
			$this->currentPart->partial = $this->route['part'] . '/' . $this->currentPart->partial;
		$this->view->template = $this->currentPart->template;
		$this->view->partial = $this->currentPart->partial;
		$this->view->partname = $this->route['part'];
		$this->view->sessionid = Session_Session::$id;
		$this->view->server = $this->serverName;
		$this->view->currentUser = $this->user;
		$this->additionalProcessResult();
		$content = $this->view->render();
		if(Config::getInstance()->useCache && Route_Router::$matched_route->cache && $this->route['part'] != 'error')
			Cache_Lite_Output::instance()->save($content, Route_Router::$match_cache['original']);
		echo Utils_CacheBlock::process($content);
	}
	
	protected function additionalProcessResult()
	{} 
	
	protected function processFormResult()
	{
		$cl = get_class($this->currentPart);
		Part_BasePart::setJsonHeaders();
		echo json_encode($this->currentPart->formResponse);
	}
	
	protected function filterFormData()
	{
		$result = array();
		$slashesAdded = get_magic_quotes_gpc() == 1;
		foreach($_POST as $key=>$value)
		{
			$v = $value;
			if(is_numeric($v))
			{
				$result[$key] = $v;
				continue;
			}
			if(!$slashesAdded)
				$v = addslashes($v);
			$v = DB_MySQL::escape($v);
			if($v === "false")
				$v = 0;
			if($v === "true")
				$v = 1;
			$result[$key] = $v;
		}
		return $result;
	}

	protected function initDataBase ()
	{
		$config = Config::getInstance();
		$tables = $this->db->getTables();
		$tableCount = count($tables);
		$find = false;
		if ($this->db->prefix != '') {
			for ($i = 0; $i < count($tables); $i ++)
				if (strstr($tables[$i], $this->db->prefix) !== false) {
					$find = true;
					break;
				}
		}
		if (! $find)
			$tableCount = 0;
		foreach ($config->db as $key => $value) {
			$prefix = $this->db->prefix;
			$find = false;
			for ($i = 0; $i < count($tables); $i ++)
				if ($tables[$i] == $prefix . $key)
					$find = true;
			if (! $find) {
				$tables[] = $prefix . $key;
				$this->db->query(str_replace($key, $prefix . $key, $value));
			}
		}
		if ($tableCount == 0 && $config->initQueries) {
			for ($i = 0; $i < count($config->initQueries); $i ++)
				$this->db->query(preg_replace('/([^`]+`)([^`]+)(`.+)/i', '\1' . $this->db->prefix . '\2\3', $config->initQueries[$i]));
		}
	}
}
?>