<?php
class Utils_CacheBlock
{
	public static function process($content)
	{
		$blocks = Config::getInstance()->blocks;
		for($i = 0; $i < count($blocks); $i++)
			$content = str_replace('<#'.$blocks[$i].'#>', self::parseblock($blocks[$i]), $content);
		return $content;
	}
	
	public static function parseblock($name){
		ob_start();
		include 'blocks/'.$name.'.phtml';
		$str = ob_get_contents();
		ob_end_clean();
		return $str;
	}
}
?>