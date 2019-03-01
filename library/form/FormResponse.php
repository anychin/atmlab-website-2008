<?

class FormResponse
{
	public $errors = array ( );
	
	public $valid = true;
	
	public $data = array();
	
	public function addError($error, $val) {
		$this->errors [$error] = $val;
		$this->valid = false;
	}
	
	public function addData($name, $value)
	{
		$this->data[$name] = $value;
	}
	
	public function removeError($error) {
		unset ( $this->errors [$error] );
	}
	
	public function getError($error) {
		return $this->errors [$error];
	}
	
	public function isValid() {
		return count ( $this->errors ) == 0;
	}
	
	public function marshall() {
		$xml = new DOMDocument ( );
		$root = $xml->createElement ( "response" );
		$xml->appendChild ( $root );
		$errors = $xml->createElement ( "errors" );
		foreach ( $this->errors as $key => $value )
		{
			$error = $xml->createElement ( "error" );
			$attr = $xml->createAttribute ( "name" );
			$attr->value = $key;
			$error->appendChild ( $attr );
			$attr = $xml->createAttribute ( "value" );
			$attr->value = $value;
			$error->appendChild ( $attr );
			$errors->appendChild ( $error );
		}
		$d = $xml->createElement ( "data" );
		foreach ( $this->data as $key => $value )
		{
			$attr = $xml->createAttribute($key);
			$attr->value = $value;
			$d->appendChild($attr);
		}
		$root->appendChild($errors);
		$root->appendChild($d);
		$valid = $xml->createAttribute ( "valid" );
		$valid->value = "false";
		if ($this->isValid ())
			$valid->value = "true";
		$root->appendChild ( $valid );
		return $xml;
	}
}

?>