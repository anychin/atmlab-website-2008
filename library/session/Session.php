<?
class Session
{
	public static $id;
	
	public function start()
	{
		session_name('atmsessid');
		session_start();
		self::$id = session_id();
	}
	
	public function end()
	{
		session_destroy();
	}
	
	public function commit()
	{
		session_commit();
	}
	
	public function getData($name)
	{
		if($_SESSION[$name])
			return $_SESSION[$name];
		return null;	
	}
	
	public function setData($name,$value)
	{
		$_SESSION[$name] = $value;
	}
	
	public function unsetData($name)
	{
		unset($_SESSION[$name]);
	}
}
?>