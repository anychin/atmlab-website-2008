<?php
class Expression
{
	public static function eq($field, $value)
	{
		return "$field = " . self::prepareValue($value);
	}
	
	public static function ne($field, $value)
	{
		return "$field != " . self::prepareValue($value);
	}
	
	public static function gt($field, $value)
	{
		return "$field > " . self::prepareValue($value);
	}
	
	public static function lt($field, $value)
	{
		return "$field < " . self::prepareValue($value);
	}
	
	public static function like($field, $value)
	{
		return "$field LIKE '%$value%'";
	}
	
	public static function likeLeft($field, $value)
	{
		return "$field LIKE '$value%'";
	}
	
	public static function likeRight($field, $value)
	{
		return "$field LIKE '%$value'";
	}
	
	public static function query($query)
	{
		return $query;
	}
	
	public static function isNotNull($field)
	{
		return "$field IS NOT NULL";
	}
	
	public static function isNull($field)
	{
		return "$field IS NULL";
	}
	
	public static function disunction($expressions)
	{
		$str = "";
		for($i = 0; $i < count($expressions); $i++)
		{
			if($i > 0)
				$str .= " OR ";
			$str .= $expressions[$i];
		}
		if($str != "")
			$str = "( " . $str . " )";
		return $str;
	}
	
	public static function conjunction($expressions)
	{
		$str = "";
		for($i = 0; $i < count($expressions); $i++)
		{
			if($i > 0)
				$str .= " AND ";
			$str .= $expressions[$i];
		}
		if($str != "")
			$str = "( " . $str . " )";
		return $str;
	}
	
	public static function in($field, $values)
	{
		$str = "";
		$n = count($values);
		for($i = 0; $i < $n; $i++)
			$str .= self::prepareValue($values[$i]) . ",";
		$str = preg_replace("/,$/", "", $str);
		return "$field in ($str)";
	}
	
	private static function prepareValue($value)
	{
		if(DAOService::decorateField($value))
			$value = "'$value'";
		return $value;
	}
}
?>