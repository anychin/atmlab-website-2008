<?
class Dao_Order
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
		return new self($name, 'desc');
	}
	
	public static function asc($name)
	{
		return new self($name, 'asc');
	}
	
	public function __toString()
	{
		return $this->name .' '.$this->sort;
	}
}
?>