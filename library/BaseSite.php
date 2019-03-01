<?
class BaseSite
{
	protected $url;
	
	protected $currentPart;
	
	protected $db;
	
	protected $serverName;
	
	public $auth;
	
	protected $partConfig;
	
	protected $dataBase;
	
	protected $defaultUserRoles;
	
	public static $instance;
	
	protected $form = false;
	
	public $user = null;
	
	protected $languages;
	
	protected $currentLanguage;
	
	public function __construct($dataBase, $partConfig, $initBase = true, $defaultUserRoles = null, $languages = null)
	{
		$this->partConfig = $partConfig;
	    if($defaultUserRoles != null)
	    	$this->defaultUserRoles = $defaultUserRoles;
	    else
	    	$this->defaultUserRoles = array(UserRoles::USER_ROLE);
	    if($languages == null)
			$this->languages = array(Config::get("defaultLanguage"));
		else
			$this->languages = $languages;
		$this->currentLanguage = Config::get("defaultLanguage");
		self::$instance = $this;
	    Session::start();
	    $this->db = new MySQL();
	    $this->db->commonTables = array_flip($dataBase['commonTables']);
		$this->dataBase = $dataBase;
		DAOService::$bd = $this->db;
		$this->resolveLanguage();
		if($this->currentLanguage != Config::get("defaultLanguage"))
			$this->db->languagePrefix .= $this->currentLanguage."_";
		Dictionary::$language = $this->currentLanguage;
		$this->auth = new SiteAuth($this->db);
		if($initBase)
			$this->initDataBase();
		$this->parseUrl();
	}
	
	public function go()
	{
		$this->configure();
		$this->display();
	}
	
	protected function resolveLanguage()
	{
		$serverNameParts = explode(".", $_SERVER['SERVER_NAME']);
		if(count($serverNameParts) > 1){
			if(in_array($serverNameParts[0], $this->languages))
				$this->currentLanguage = $serverNameParts[0];
		}
		$this->serverName = $_SERVER['SERVER_NAME'];
	}
	
	protected function parseUrl()
	{
		$url = $_SERVER['REQUEST_URI'];
		$pos = strpos($url, "?");
		if($pos !== false)
			$url = substr($url, 0, $pos);
		$url = preg_replace("/^\//",'',$url);
		$url = preg_replace("/\/$/",'',$url);
		$this->url = explode("/",$url);
	}
	
	protected function configure()
	{
		if($this->url[0] == "form"){
		    $this->form = true;
		    $this->url= ArrayUtil::splice($this->url, 1);
		}
		$this->configurePart($this->url[0]);
	}
	
	protected function configurePart($name)
	{
		$part = PartFactory::getPart($this->partConfig, $name);
		$this->currentPart = new $part['part']($part['name']);
		$this->currentPart->db = $this->db;
		if($part['name'] != $name && $name != "")
			$this->currentPart->url = $this->url;
		else
			$this->currentPart->url = ArrayUtil::splice($this->url, 1);
	}
	
	public function changePart($name){
		$this->currentPart->output = false;
		$this->configurePart($name);
		$this->currentPart->display();
	}
	
	protected function display()
	{
		if($this->form)
			$this->currentPart->formMethod();
		else{
			try{
				$this->displayPart();
			}catch(PageNotFoundException $e){
				header("HTTP/1.0 404 Not Found");
				$this->changePart("error");
			}
		}
			
	}
	
	protected function displayPart()
	{
		Template::add("sessionid", Session::$id);
		Template::add("server", $this->serverName);
		Template::add("currentUser", $this->user);
		Template::add("defaultLanguage", Config::get("defaultLanguage"));
		Template::add("currentLanguage", $this->currentLanguage);
		Template::add("languages", $this->languages);
		$this->currentPart->display();
	}
	
	protected function initDataBase()
	{
		global $initQuerys;
		$tables = $this->db->getTables();
		if(!isset($this->dataBase))
			return;
		$tableCount = count($tables);
		$find = false;
		if($this->db->prefix != ''){
			for($i = 0; $i < count($tables); $i++)
				if(strstr($tables[$i], $this->db->prefix) !== false){
					$find = true;
					break;
				}		
		}
		if(!$find)
			$tableCount = 0;
		$common = $this->dataBase['commonTables'];
		foreach($this->dataBase as $key=>$value)
		{
			if($key == "commonTables")
				continue;
			for($j = 0; $j < count($this->languages); $j++){
				$prefix = $this->db->prefix;
				if($this->languages[$j] != Config::get("defaultLanguage"))
					$prefix = $this->db->prefix.$this->languages[$j]."_";
				if(in_array($key, $common)){
					$prefix = $this->db->prefix;
				}
				$find = false;
				for($i = 0; $i < count($tables); $i++)
					if($tables[$i] == $prefix.$key)
						$find = true;
				if(!$find){
					$tables[] = $prefix.$key;
					$this->db->query(str_replace($key, $prefix.$key, $value));
				}
			}
		}
		if($tableCount == 0 && isset($initQuerys))
			for($j = 0; $j < count($this->languages); $j++){
				$prefix = $this->db->prefix;
				if($this->languages[$j] != Config::get("defaultLanguage"))
					$prefix .= $this->languages[$j]."_";
				for($i = 0; $i < count($initQuerys); $i++)
					$this->db->query(preg_replace('/([^`]+`)([^`]+)(`.+)/i', '\1'.$prefix.'\2\3', $initQuerys[$i]));
			}
	}
}
?>