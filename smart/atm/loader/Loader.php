<?php
class Loader
{
	public static function loadClass($name)
	{
		if(class_exists($name, false) || interface_exists($name, false))
			return;
		$fileName = self::getFileName($name);
		@require_once $fileName;
		if(!class_exists($name) && !interface_exists($name))
			throw new Exception('class '.$name.'.php has not been found');
	}
	
	public static function checkClassExistance($name)
	{
		if(class_exists($name, false) || interface_exists($name, false))
			return true;
		$fileName = self::getFileName($name);
		@require_once $fileName;
		return class_exists($name, false) || interface_exists($name, false);
	}
	
	private static function getFileName($name)
	{
		$parts = explode('_', $name);
		if(count($parts) == 1)
		{
			$fileName = $name;
		}
		else
		{	
			$parts = array_splice($parts, -1);
			$fileName = $parts[0];
			$n = str_replace('_', DIRECTORY_SEPARATOR, $name);
			$n = preg_replace('/'.$fileName.'$/', '', $n);
			$fileName = strtolower($n).$fileName;
		}
		return $fileName.'.php';
	}
}

function __autoload($name){
	Loader::loadClass($name);
}
?>