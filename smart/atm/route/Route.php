<?php
class Route_Route
{

	protected $route;

	protected $parts = array();

	protected $vars = array();

	protected $defaults = array();

	protected $requirements = array();

	protected $vals = array();

	protected $staticCount = 0;

	protected $wildcardData = array();
	
	protected $zone;
	
	public $cache = false;

	public function __construct ($route, $defaults, $requirements, $zone = '', $cache = true)
	{
		$this->route = trim($route, '/');
		$this->defaults = $defaults;
		$this->requirements = $requirements;
		$this->zone = $zone;
		$this->cache = $cache;
		$this->parseUrl();
	}

	protected function parseUrl ()
	{
		if ($this->route == '')
			return false;
		foreach (explode('/', $this->route) as $i => $part) {
			if (substr($part, 0, 1) == ':') {
				$name = substr($part, 1);
				$this->vars[$i] = $name;
				$this->parts[$i] = isset($this->requirements[$name]) ? $this->requirements[$name] : null;
			} else {
				$this->parts[$i] = $part;
				if ($part != '*')
					$this->staticCount ++;
			}
		}
	}

	public function match ($url)
	{
		$pathStaticCount = 0;
		$values = array();
		$matchedPath = '';
		if($this->zone != '')
			$url = str_replace($this->zone, '', $url);
		$original = $url;
		$url = trim($url, '/');
		$url = explode('/', $url);
		$count = count($url);
		if ($count > 0) {
			foreach ($url as $pos => $pathPart) {
				if (! array_key_exists($pos, $this->parts))
					return false;
				$matchedPath .= $pathPart . '/';
				if ($this->parts[$pos] == '*') {
					for ($i = $pos; $i < $count; $i += 2) {
						$var = urldecode($url[$i]);
						if (! isset($this->wildcardData[$var]) && ! isset($this->defaults[$var]) && ! isset($values[$var]))
							$this->wildcardData[$var] = (isset($url[$i + 1])) ? urldecode($url[$i + 1]) : null;
					}
					break;
				}
				$name = isset($this->vars[$pos]) ? $this->vars[$pos] : null;
				$pathPart = urldecode($pathPart);
				$part = $this->parts[$pos];
				if ($name === null && $part != $pathPart)
					return false;
				if ($part !== null && ! preg_match('#^' . $part . '$#iu', $pathPart))
					return false;
				if ($name !== null)
					$values[$name] = $pathPart;
				else
					$pathStaticCount ++;
			}
		}
		if ($this->staticCount != $pathStaticCount)
			return false;
		$return = $values + $this->wildcardData + $this->defaults;
		foreach ($this->vars as $var) {
			if (! array_key_exists($var, $return))
				return false;
		}
		$return['original'] = $original;
		$return['parsedUrl'] = $url;
		$this->vals = $values;
		return $return;
	}
}
?>