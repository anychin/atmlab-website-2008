<?php

class Dao_Field
{
	protected $vars = array();
	
	public function __set($f, $v)
	{
		$this->vars[$f] = $v;
	}
	
	public function __get($field)
	{
		return stripslashes($this->vars[$field]);
	}
	
	public function __toString()
	{
		if($this->id)
			return $this->id;
		return get_class($this);
	}
}

?>