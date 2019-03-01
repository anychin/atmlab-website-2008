<?php
class Db_Image_Image
{
	private $res = null;
	
	public function __get($field)
	{
		if($field == 'resizes')
			return $this->getResizes();
		if(!isset($this->$field))
			return null;
		return $this->$field;
	}
	
	public function getResizes()
	{
		if($this->res != null)
			return $this->res;
		$resizes = Db_Image_DAO::instance()->getResizes($this->id);
		$this->res = array();
		for($i = 0; $i < count($resizes); $i++)
			$this->res[$resizes[$i]->name] = $resizes[$i];
		return $this->res;
	}
	
	public function serialize()
	{
		$obj = clone $this;
		$obj->resizes = $this->resizes;
		return json_encode($obj);
	}
}
?>