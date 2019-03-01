<?
class FormController
{
	private $response = null;
	
	private static $controller = null;
	
	private function __construct()
	{
		$this->response = new FormResponse();
	}
	
	public static function getData()
	{
		self::getThis();
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
			if($slashesAdded)
				$v = stripslashes($v);
			$v = mysql_real_escape_string($v);
			if($v === "false")
				$v = 0;
			if($v === "true")
				$v = 1;
			$result[$key] = $v;
		}
		return $result;
	}
	
	public static function addError($name, $value)
	{
		self::getThis();
		self::$controller->response->addError($name, $value);
	}
	
	public static function addData($name, $value)
	{
		self::getThis();
		self::$controller->response->addData($name, $value);
	}
	
	public static function getError($name)
	{
		self::getThis();
		return self::$controller->response->getError($name);
	}
	
	public static function removeError($name)
	{
		self::getThis();
		return self::$controller->response->removeError($name);
	}
	
	public static function sendData()
	{
		self::getThis();
		BasePart::setNoCahceHeaders();
		BasePart::setXMLHeaders();
		echo self::$controller->response->marshall()->saveXML();
	}
	
	public static function isValid()
	{
		self::getThis();
		return self::$controller->response->isValid();
	}
	
	private static function getThis()
	{
		if(self::$controller == null)
			self::$controller = new FormController();
	}
}
?>