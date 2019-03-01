<?
class Part_BasePart
{
	
	public static $instance = null;
	
	public $route;
	
	protected $title;
	
	protected $keywords;
	
	protected $description;
	
	public $output = true;
	
	public $layout = null;
	
	public $template = null;
	
	public $partial = null;
	
	public $view;
	
	public $formResponse;
	
	public function __construct()
	{
	    $this->title = "";
		$this->keywords = "";
		$this->description = "";
		$this->formResponse = new stdClass();
		$this->formResponse->valid = true;
		$this->formResponse->errors = array();
		$this->formResponse->data = array();
		self::$instance = $this;
	}
	
	public function index()
	{
	}
	
	public function error()
	{
		
	}
	
	public function before()
	{
		
	}
	
	public function after()
	{
		$this->view->title = $this->title;
		$this->view->keywords = $this->keywords;
		$this->view->description = $this->description;
	}
	
	public static function navigate($address)
	{
		if(self::$instance != null)
			self::$instance->output = false;
		header("location: ".$address);
	}
	
	public function form($data)
	{
	}
	
	protected function addFormError($field, $error)
	{
		$this->formResponse->errors[$field] = $error;
		$this->formResponse->valid = false;
	}
	
	protected function addFormData($name, $value)
	{
		$this->formResponse->data[$name] = $value;
	}
	
	protected function formIsValid()
	{
		return $this->formResponse->valid;
	}
	
	public static function setNoCahceHeaders()
	{
		header("Pragma: no-cache");
		header("Cache-Control: no-cache");
		header("Expires: Tue, 01 Jan 1981 01:00:00 GMT");
	}
	
	public static function setXMLHeaders()
	{
	    header("Content-Type: text/xml; charset=utf-8");
	}
	
	public static function setJsonHeaders()
	{
	    header("Content-Type: application/json; charset=utf-8");
	}
	
	public static function xmlOut($value, $cache = false)
	{
	    self::setXMLHeaders();
		if($cache)
		    self::setNoCahceHeaders();
		echo $value;
	}
	
	public static function escape($str)
	{
		if(get_magic_quotes_gpc() == 1)
			$str = stripslashes($str);
		return DB_MySQL::escape($str);
	}
	
	protected function parsePage($name = 'page', $max = 0)
	{
		$page = 0;
		if(is_numeric($name))
		{
			$max = $name;
			$name = 'page';
		}
		if(isset($this->route[$name]))
			$page = intval($this->route[$name]) - 1;
		if($page < 0 || ($page > $max && $max > 0))
			$page = 0;
		return $page;
	}
}
?>