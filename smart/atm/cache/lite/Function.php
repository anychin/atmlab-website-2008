<?php

class Cache_Lite_Function extends Cache_Lite_Lite
{
	
	private static $ins = null;
	
	public static function instance()
	{
		if(self::$ins == null)
		{
			$c = Config::getInstance();
			self::$ins = new self(array('cacheDir' => $c->cacheDir, 'lifeTime' => $c->lifeTime, 'readControlType'=>'md5'));
		}
		return self::$ins;
	} 

    var $_defaultGroup = 'Cache_Lite_Function';

    var $_dontCacheWhenTheOutputContainsNOCACHE = false;

    var $_dontCacheWhenTheResultIsFalse = false;

    var $_dontCacheWhenTheResultIsNull = false;

    var $_debugCacheLiteFunction = false;

    public function __construct($options = array(NULL))
    {
        $availableOptions = array('debugCacheLiteFunction', 'defaultGroup', 'dontCacheWhenTheOutputContainsNOCACHE', 'dontCacheWhenTheResultIsFalse', 'dontCacheWhenTheResultIsNull');
        while (list($name, $value) = each($options)) {
            if (in_array($name, $availableOptions)) {
                $property = '_'.$name;
                $this->$property = $value;
            }
        }
        reset($options);
        parent::__construct($options);
    }

    function call()
    {
        $arguments = func_get_args();
        $id = $this->_makeId($arguments);
        $data = $this->get($id, $this->_defaultGroup);
        if ($data !== false) {
            if ($this->_debugCacheLiteFunction) {
                echo "Cache hit !\n";
            }
            $array = unserialize($data);
            $output = $array['output'];
            $result = $array['result'];
        } else {
            if ($this->_debugCacheLiteFunction) {
                echo "Cache missed !\n";
            }
            ob_start();
            ob_implicit_flush(false);
            $target = array_shift($arguments);
            if (is_array($target)) {
                // in this case, $target is for example array($obj, 'method')
                $object = $target[0];
                $method = $target[1];
                $result = call_user_func_array(array(&$object, $method), $arguments);
            } else {
                if (strstr($target, '::')) { // classname::staticMethod
                    list($class, $method) = explode('::', $target);
                    $result = call_user_func_array(array($class, $method), $arguments);
                } else if (strstr($target, '->')) { // object->method
                    // use a stupid name ($objet_123456789 because) of problems where the object
                    // name is the same as this var name
                    list($object_123456789, $method) = explode('->', $target);
                    global $$object_123456789;
                    $result = call_user_func_array(array($$object_123456789, $method), $arguments);
                } else { // function
                    $result = call_user_func_array($target, $arguments);
                }
            }
            $output = ob_get_contents();
            ob_end_clean();
            if ($this->_dontCacheWhenTheResultIsFalse) {
                if ((is_bool($result)) && (!($result))) {
                    echo($output);
                    return $result;
                }
            }
            if ($this->_dontCacheWhenTheResultIsNull) {
                if (is_null($result)) {
                    echo($output);
                    return $result;
                }
            }
            if ($this->_dontCacheWhenTheOutputContainsNOCACHE) {
                if (strpos($output, 'NOCACHE') > -1) {
                    return $result;
                }
            }
            $array['output'] = $output;
            $array['result'] = $result;
            $this->save(serialize($array), $id, $this->_defaultGroup);
        }
        echo($output);
        return $result;
    }

    function drop()
    {
        $id = $this->_makeId(func_get_args());
        return $this->remove($id, $this->_defaultGroup);
    }

    function _makeId($arguments)
    {
        $id = serialize($arguments); // Generate a cache id
        if (!$this->_fileNameProtection) {
            $id = md5($id);
        }
        return $id;
    }

}
?>