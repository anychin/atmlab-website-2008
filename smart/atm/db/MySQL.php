<?
class DB_MySQL
{
	private $host;
	
	private $user;
	
	private $password;
	
	private $database;
	
	private $connector;
	
	public $prefix = "";
	
	public static $instance = null;
	
	public function __construct($params = null)
	{
		if($params != null)
		{
			if(isset($params['host']))
				$this->host = $params['host'];
			else
				$this->host = "localhost";
			if(isset($params['user']))
				$this->user = $params['user'];
			else
				$this->user = "root";
			if(isset($params['password']))
				$this->password = $params['password'];
			else
				$this->password = "";
			if(isset($params['database']))
				$this->database = $params['database'];
		}
		else
		{
			$this->host = HOST;
			$this->user = USER;
			$this->password = PASSWORD;
			$this->database = DB_NAME;
			$this->prefix = DB_PREFIX;
		}
		$this->connect();
		self::$instance = $this;
	}
	
	public static function getMysqli()
	{
		return self::$instance->connector;
	}
	
	public function getData($sql, $clazz = false)
	{
		$result = $this->connector->query($sql);
	    if(!$result)
			return null;
		if($this->connector->affected_rows == 0)
			return array();
		$res = array();
		if($clazz){
			while(($obj = $result->fetch_object($clazz)) != null)
				$res[] = $obj;
		}else{
			while(($obj = $result->fetch_object()) != null)
				$res[] = $obj;
		}
		return $res;	
	}
	
	public function getUniqueData($sql, $clazz = null)
	{
		if($sql == '')
			return null;
		$result = $this->connector->query($sql);
		if(!$result)
			return null;
		if($this->connector->affected_rows == 0)
			return null;
		if(!$clazz)
			return $result->fetch_object();
		return $result->fetch_object($clazz);
	}
	
	public function query($sql)
	{
		if($sql == '')
			return null;
		$sect = $this->connector->query($sql);
		if($sect === false)
			return null;
		return true;	
	}
	
	public function insert($sql)
	{
		if($this->query($sql))
			return $this->connector->insert_id;
		return null;	
	}
	
	public function connect()
	{
		@$this->connector = new mysqli($this->host, $this->user, $this->password, $this->database);
		if(!$this->connector || mysqli_connect_errno())
			die("Невозможно соединиться с сервером или база данных не найдена");
		@$this->connector->set_charset('utf8');
		$this->query("set character_set_client='utf8'");
		$this->query("set character_set_results='utf8'");
		$this->query("set collation_connection='utf8_general_ci'");	
	}
	
	public function disconnect()
	{
		$this->connector->close();
	}
	
	public function getTables()
	{
		$result = array();
		$result = $this->connector->query('show tables');
		$res = array();
		while(($obj = $result->fetch_array()) != null)
			$res[] = $obj[0];
		return $res;
	}
	
	public static function escape($str)
	{
		if(is_numeric($str))
			return $str;
		$mysqli = self::getMysqli();
		return $mysqli->real_escape_string($str);
	}
}
?>