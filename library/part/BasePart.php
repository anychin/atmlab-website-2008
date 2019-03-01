<?
class BasePart
{
	public $url;
	
	protected $title;
	
	protected $keywords;
	
	protected $description;
	
	protected $partName;
	
	public $db;
	
	public $output = true;
	
	protected $templateFile = SITE_TEMPLATE;
	
	public static $instance;
	
	public function __construct($name)
	{
	    $this->title = "";
		$this->keywords = "";
		$this->description = "";
		$this->partName = $name;
		self::$instance = $this;
	}
	
	public static function navigate($address)
	{
		if($this)
			$this->output = false;
		header("location: ".$address);
	}
	
	protected function doAll()
	{
		Template::add("url", $this->url);
		Template::add("partName", $this->partName);
		Template::add("title", $this->title);
		Template::add("keywords", $this->keywords);
		Template::add("description", $this->description);
	}
	
	public function display()
	{
		$this->doAll();
		if(!$this->output)
			return;
		include ("templates/".$this->templateFile);
	}
	
	public static function render($templateFile)
	{
		include ("templates/".$templateFile);
		$str = ob_get_contents();
		ob_clean();
		return $str;
	}
	
	public function formMethod()
	{
		return FormController::getData();
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
	
	public static function xmlOut($value, $cache = false)
	{
	    self::setXMLHeaders();
		if($cache)
		    self::setNoCahceHeaders();
		echo $value;
	}
	
	public static function escape($str)
	{
		if(is_numeric($str))
			return $str;
		if(get_magic_quotes_gpc() == 1)
			$str = stripslashes($str);
		return mysql_real_escape_string($str);
	}
}
?>