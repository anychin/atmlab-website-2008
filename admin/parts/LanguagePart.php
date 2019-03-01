<?
class LanguagePart extends Part
{
	function __construct($name)
	{
		parent::__construct($name);
	}
	
	public function doAll()
	{
		$this->output = false;
		if(isset($_GET[lang]))
			Session::setData('language', $_GET[lang]);
	}
}
?>