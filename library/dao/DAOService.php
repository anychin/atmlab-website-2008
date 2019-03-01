<?
include ('Order.php');
include ('Expression.php');

class DAOService
{
	public static $bd;

	protected $table = null;
	
	public static $instances = array();
	
	public static $tableNameCache = array();

	public function getTable()
	{
		return $this->table;
	}

	public function __construct($table = null)
	{
		$this->table = $table;
		if(!isset(self::$instances[get_class($this)]))
			self::$instances[get_class($this)] = $this;
	}
	
	public static function instance($className)
	{
		if(!isset(self::$instances[$className]))
			self::$instances[$className] = new $className();
		return self::$instances[$className];
	}

	public function get($id, $criteria = "", $fields = array(), $table = null)
	{
		if($table == null)
			$table = $this->table;
		$table = $this->resolveTable($table);
		$sel = $this->getSelectFields($fields);
		$query = "SELECT $sel FROM {$table}";
		$id = intval($id);
		if($id > 0)
			$query .= " WHERE `id` = $id";
		$criteria = $this->processCriteria($criteria);
		if($criteria != "")
		{
			if($id > 0)
				$query .= " AND " . $criteria;
			else
				$query .= " WHERE " . $criteria;
		}
		return self::$bd->getUniqueData($query);
	}

	public function count($criteria = "", $table = null)
	{
		$var = "count(*)";
		$criteria = $this->processCriteria($criteria);
		if($criteria != "")
			$criteria = "WHERE $criteria";
		if($table == null)
			$table = $this->table;
		$table = $this->resolveTable($table);
		$result = self::$bd->getUniqueData("SELECT count(*) FROM {$table} $criteria");
		return $result->$var;
	}

	public function getList($criteria = "", $page = -1, $itemsPerPage = -1, $orders = array(), $fields = array(), $table = null)
	{
		$sel = $this->getSelectFields($fields);
		if($table == null)
			$table = $this->table;
		$table = $this->resolveTable($table);
		$query = "SELECT $sel FROM {$table}";
		$criteria = $this->processCriteria($criteria);
		if($criteria != "")
			$query .= " WHERE " . $criteria;
		if($orders != null && count($orders) > 0)
		{
			$query .= " ORDER BY";
			for($i = 0; $i < count($orders); $i ++)
				$query .= " {$orders[$i]->name} {$orders[$i]->sort},";
			$query = preg_replace("/,$/", "", $query);
		}
		if($page != - 1 && $itemsPerPage != - 1)
			$query .= " LIMIT " . ($page * $itemsPerPage) . "," . $itemsPerPage;
		return self::$bd->getData($query);
	}

	public function delete($id, $criteria = "", $table = null)
	{
		$this->deleteFilter($id, $criteria, $table);
		if($table == null)
			$table = $this->table;
		$table = $this->resolveTable($table);
		$sql = "DELETE FROM {$table} WHERE `id` = $id";
		$criteria = $this->processCriteria($criteria);
		if($criteria != "")
			$sql = "DELETE FROM {$table} WHERE $criteria";
		self::$bd->query($sql);
	}
	
	protected function createFilter($values = array())
	{
		return $values;
	}
	
	protected function saveFilter($values, $criteria, $table)
	{
		return $values;
	}
	
	protected function deleteFilter($id, $criteria, $table)
	{
		
	}
	
	public function insert($values = array(), $table = null)
	{
		$values = $this->createFilter($values);
		$names = "";
		$vals = "";
		if($table == null)
			$table = $this->table;
		$table = $this->resolveTable($table);
		foreach($values as $key => $value)
		{
			$names .= "$key,";
			if(!self::decorateField($value))
				$vals .= "$value,";
			else
				$vals .= "'$value',";
		}
		if($names != "")
		{
			$names = preg_replace('/,$/', '', $names);
			$vals = preg_replace('/,$/', '', $vals);
		}
		$id = self::$bd->insert("INSERT INTO {$table} ($names) VALUES ($vals)");
		return $this->get($id);
	}

	public function update($values, $criteria = "", $table = null)
	{
		$values = $this->saveFilter($values, $criteria, $table);
		if($table == null)
			$table = $this->table;
		$table = $this->resolveTable($table);
		$query = "";
		$criteria = $this->processCriteria($criteria);
		foreach($values as $key => $value)
		{
			if($key == "id")
				continue;
			$query .= " $key = ";
			if(!self::decorateField($value)){
				$query .= "$value,";
			}
			else
				$query .= "'$value',";
		}
		$query = preg_replace('/,$/', '', $query);
		if(isset($values['id']))
			$query = "UPDATE {$table} SET $query WHERE `id` = {$values['id']}";
		else
		{
			if($criteria != "")
				$query = "UPDATE {$table} SET $query WHERE $criteria";
			else
				$query = "UPDATE {$table} SET $query WHERE 1";
		}
		self::$bd->query($query);
		return $this->get($values['id']);
	}
	
	private function getSelectFields($fields){
		$sel = "*";
		if(count($fields) > 0)
		{
			$sel = "";
			for($i = 0; $i < count($fields); $i++)
				$sel .= $fields[$i].",";
			$sel = preg_replace('/,$/','', $sel);
		}
		return $sel;
	}
	
	protected function processCriteria($criteria)
	{
		$str = $criteria;
		if(is_array($criteria))
		{
			$str = "";
			for($i = 0; $i < count($criteria); $i++)
			{
				if($i > 0)
					$str .= " AND ";
				$str .= $criteria[$i];
			}
		}
		return $str;
	}
	
	public static function decorateField($value)
	{
		if(is_numeric($value) && "".$value[0] != "0")
			return false;
		return true;
	}
	
	public function resolveTable($table)
	{
		$table = trim($table);
		if(isset(self::$tableNameCache[$table]))
			return self::$tableNameCache[$table];
		$names = explode(',', $table);
		$result = array();
		for($i = 0; $i < count($names); $i++)
		{
			$n = trim($names[$i]);
			if(isset(self::$bd->commonTables[$n]))
			{
				$result[] = self::$bd->prefix.$n;
				continue;
			}
			$result[] = self::$bd->languagePrefix.$n;
		}
		self::$tableNameCache[$table] = join(',', $result);
		return self::$tableNameCache[$table];
	}
}
?>
