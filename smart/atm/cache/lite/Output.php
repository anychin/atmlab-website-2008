<?php
class Cache_Lite_Output extends Cache_Lite_Lite
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

	public function __construct($options)
    {
    	parent::__construct($options);
    }

    function start($id, $group = 'default', $doNotTestCacheValidity = false)
    {
        $data = $this->get($id, $group, $doNotTestCacheValidity);
        if ($data !== false) {
            echo($data);
            return true;
        }
        ob_start();
        ob_implicit_flush(false);
        return false;
    }

    function end()
    {
    	$data = ob_get_contents();
        ob_end_clean();
        $this->save($data, $this->_id, $this->_group);
        echo($data);
    }

}
?>