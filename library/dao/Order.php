<?
class Order
{
	public $name;
	
	public $sort;
	
	private function __construct($name, $sort)
	{
		$this->name = $name;
		$this->sort = $sort;
	}
	
	public static function desc($name)
	{
		return new Order($name, 'desc');
	}
	
	public static function asc($name)
	{
		return new Order($name, 'asc');
	}
}
?>