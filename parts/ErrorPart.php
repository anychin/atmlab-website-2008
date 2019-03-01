<?
class ErrorPart extends Part
{
	public function __construct($name)
	{
		parent::__construct($name);
	}
	
	public function doAll()
	{
		$this->title = "Ошибка";
		parent::doAll();
	}
}

?>