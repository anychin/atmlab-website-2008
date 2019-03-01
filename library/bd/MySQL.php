<?
class MySQL
{
	private $host;
	
	private $user;
	
	private $password;
	
	private $database;
	
	private $connector;
	
	public $prefix = "";
	
	public $languagePrefix = '';
	
	public $commonTables;
	
	public function __construct($params = null)
	{
		if($params != null)
		{
			if(isset($params[host]))
				$this->host = $params[host];
			else
				$this->host = "localhost";
			if(isset($params[user]))
				$this->user = $params[user];
			else
				$this->user = "root";
			if(isset($params[password]))
				$this->password = $params[password];
			else
				$this->password = "";
			if(isset($params[database]))
				$this->database = $params[database];
		}
		else
		{
			$this->host = HOST;
			$this->user = USER;
			$this->password = PASSWORD;
			$this->database = DB_NAME;
			$this->prefix = DB_PREFIX;
		}
		$this->languagePrefix = $this->prefix;
		$this->connect();
	}
	
	public function getData($sql)
	{
		@$sect = mysql_query($sql, $this->connector);
	    if(!$sect)
			return null;
		if(mysql_num_rows($sect) == 0)
			return array();
		$result = array();
		while($sectRes = mysql_fetch_object($sect))
			$result[] = $sectRes;
		return $result;	
	}
	
	public function getUniqueData($sql)
	{
		if($sql == '')
			return null;
		@$sect = mysql_query($sql, $this->connector);
		if(!$sect)
			return null;
		if(mysql_num_rows($sect) == 0)
			return null;
		return mysql_fetch_object($sect);
	}
	
	public function query($sql)
	{
		if($sql == '')
			return null;
		$sect = mysql_query($sql, $this->connector);
		if($sect === false)
			return null;
		return true;	
	}
	
	public function insert($sql)
	{
		if($this->query($sql, $this->connector))
			return mysql_insert_id($this->connector);
		return null;	
	}
	
	public function connect()
	{
		@$this->connector=mysql_connect($this->host, $this->user, $this->password);
		if(!$this->connector)
			die("Невозможно соединиться с сервером");
		if(!@mysql_selectdb($this->database, $this->connector))
			die("База данных ".$this->database." не найдена или повреждена");
		
		$this->query("set character_set_client='utf8'");
		$this->query("set character_set_results='utf8'");
		$this->query("set collation_connection='utf8_general_ci'");	
	}
	
	public function disconnect()
	{
		mysql_close($this->connector);
	}
	
	public function &getTables()
	{
	    $result = array();
	    $list = mysql_list_tables($this->database);
	    $n = mysql_num_rows($list);
	    for($i = 0; $i < $n; $i++)
	        $result[] = mysql_tablename($list, $i);
	    return $result;
	}
}
?>