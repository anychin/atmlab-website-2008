<?
class Session_Session
{
	public static $id;
	
	public static function start()
	{
		session_name('atmsessid');
		session_start();
		self::$id = session_id();
	}
	
	public static function end()
	{
		session_destroy();
	}
	
	public static function commit()
	{
		session_commit();
	}
	
	public static function getData($name)
	{
		if(isset($_SESSION[$name]))
			return $_SESSION[$name];
		return null;	
	}
	
	public static function setData($name,$value)
	{
		$_SESSION[$name] = $value;
	}
	
	public static function unsetData($name)
	{
		if(isset($_SESSION[$name]))
			unset($_SESSION[$name]);
	}
}
?>