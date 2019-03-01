<?
class PartFactory
{
	public static function getPart($partConfig, $name = '')
	{
		if($name == '')
			$name = DEFAULT_PART_NAME;
		if( ! $partConfig[$name])
			$name = DEFAULT_PART_NAME;
		if(strstr($_SERVER['SCRIPT_NAME'], 'site.php') !== false)
			self::addPart($partConfig[$name]);
		return array('name'=>$name, 'part'=>$partConfig[$name]);
	}
	
	public static function addPart($name)
	{
		$file = 'parts/' . $name . '.php';
		if(file_exists($file))
			@require_once ($file);
		else
			die('Нет необходимых файлов - '.$file);
	}
}
?>