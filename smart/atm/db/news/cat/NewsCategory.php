<?php
class Db_News_Cat_NewsCategory
{
	public function __get($field)
	{
		if(!isset($this->$field))
			return null;
		return $this->$field;
	}
}
?>