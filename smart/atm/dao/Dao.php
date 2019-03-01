<?
class Dao_Dao
{
	public static $db;

	public $table = null;
	
	public static $instances = array();
	
	public static $tableNameCache = array();
	
	public $model = 'Dao_Field';

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
		if($id == 0 && $criteria == '')
			return null;
		$criteria = $this->processCriteria($criteria);
		if($criteria != "")
		{
			if($id > 0)
				$query .= " AND " . $criteria;
			else
				$query .= " WHERE " . $criteria;
		}
		return self::$db->getUniqueData($query, $this->model);
	}

	public function count($criteria = "", $table = null, $field = 'id', $distinct = false)
	{
		if($distinct)
			$var = "count(DISTINCT {$field})";
		else
			$var = "count(*)";
		$criteria = $this->processCriteria($criteria);
		if($criteria != "")
			$criteria = "WHERE $criteria";
		if($table == null)
			$table = $this->table;
		$table = $this->resolveTable($table);
		if($distinct)
			$result = self::$db->getUniqueData("SELECT count(DISTINCT {$field}) FROM {$table} $criteria");
		else
			$result = self::$db->getUniqueData("SELECT count(*) FROM {$table} $criteria");
		return $result->$var;
	}

	public function getList($criteria = "", $page = -1, $itemsPerPage = -1, $orders = array(), $fields = array(), $table = null, $distinct = false)
	{
		$sel = $this->getSelectFields($fields);
		if($table == null)
			$table = $this->table;
		$table = $this->resolveTable($table);
		if($distinct)
			$query = "SELECT DISTINCT $sel FROM {$table}";
		else
			$query = "SELECT $sel FROM {$table}";
		$criteria = $this->processCriteria($criteria);
		if($criteria != "")
			$query .= " WHERE " . $criteria;
		if($orders)
			$query .= " ORDER BY ".implode(', ', $orders);
		if($page != - 1 && $itemsPerPage != - 1)
			$query .= " LIMIT " . ($page * $itemsPerPage) . "," . $itemsPerPage;
		return self::$db->getData($query, $this->model);
	}

	public function delete($id, $criteria = "", $table = null)
	{
		if($id == 0 && $criteria == '')
			return;
		if($table == null)
			$table = $this->table;
		$table = $this->resolveTable($table);
		$id = intval($id);
		$sql = "DELETE FROM {$table} WHERE `id` = $id";
		$criteria = $this->processCriteria($criteria);
		if($criteria != "")
			$sql = "DELETE FROM {$table} WHERE $criteria";
		self::$db->query($sql);
	}
	
	public function insert($values = array(), $table = null)
	{
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
		$id = self::$db->insert("INSERT INTO {$table} ($names) VALUES ($vals)");
		if($id == null)
			return null;
		return $this->get($id);
	}

	public function update($values, $criteria = "", $table = null)
	{
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
		self::$db->query($query);
		if(isset($values['id']))
			return $this->get($values['id']);
		return null;
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
			$result[] = self::$db->prefix.$n;
		}
		self::$tableNameCache[$table] = join(',', $result);
		return self::$tableNameCache[$table];
	}
}
?>
