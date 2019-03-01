<?
class ArrayUtil
{
	public static function deleteItem($item, $array)
	{
		if(count($array) == 0)
			return array();
		$result = array();
		for($i=0; $i < count($array); $i++)
			if($array[$i] != $item)
				$result[count($result)] = $array[$i];
		return $result;		
	}
	
	public static function inArray($item, $array)
	{
		if(count($array) == 0)
			return false;
		for($i = 0; $i < count($array); $i++)
			if($array[$i] == $item)
				return true;
		return false;
	}
	
	public static function splice($array, $offset = 0)
	{
		if(!$res = @array_splice($array, $offset))
			return null;
		else 
		 	return $res;
	}
}
?>