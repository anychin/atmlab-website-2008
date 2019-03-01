<?php
class Route_Router
{
	private static $routes = array();
	
	private static $zone = '';
	
	public static $match_cache = null;
	
	public static $matched_route = null;
	
	public static function add($route, $defaults = array(), $requirements = array(), $cache = true)
	{
		self::$routes[] = new Route_Route($route, $defaults, $requirements, self::$zone, $cache);
	}
	
	public static function addZone($zone)
	{
		self::$zone = $zone;
	}
	
	public static function getRoutes()
	{
		return self::$routes;
	}
	
	public static function match($url = '', $enforce= false)
	{
		if(!$enforce && self::$match_cache !== null)
			return self::$match_cache;
		if($url == '')
			$url = $_SERVER['REQUEST_URI'];
		$pos = strpos($url, "?");
		if($pos !== false)
			$url = substr($url, 0, $pos);
		$url = trim($url, '/');
		$r = false;
		self::$routes = array_reverse(self::$routes);
		foreach(self::$routes as $i => $route)
			if($r = $route->match($url))
			{
				self::$matched_route = $route;
				break;
			}
		if(!$r)
			throw new Exception('where is no suitable route');
		self::$match_cache = $r;
		return $r;
	}
}
?>