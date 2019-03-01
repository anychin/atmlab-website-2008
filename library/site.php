<?
function checkExclude()
{
	$originalUrl = $url = $_SERVER['REQUEST_URI'];
	$pos = strpos($url, "?");
	if($pos !== false)
		$url = substr($url, 0, $pos);
	$clearUrl = $url;
	$url = preg_replace("/\/$/", '', preg_replace("/^\//", '', $url));
	$urlArray = explode("/", $url);
	if(!Config::get("useCache"))
		return false;
	if($urlArray[0] == "form")
		return false;
	if($urlArray[0] != "form")
	{
		$exclude = Config::get("cacheExclude");
		if(isset($exclude))
		{
			$match = false;
			for($i = 0; $i < count($exclude); $i++)
			{
				$u = $exclude[$i];
				$pos = strpos($u, "/**");
				if($pos !== false)
					$u = substr($u, 0, $pos);
				if($u == $clearUrl && $pos === false)
				{
					$match = true;
					break;
				}
				if(strpos($clearUrl, $u) === 0 && strpos($clearUrl . '/', $u . '/') === 0 && $pos !== false)
				{
					$match = true;
					break;
				}
			}
			if($match)
				return false;
		}
	}
	return $originalUrl;
}
$checkCache = checkExclude();
$cache = new Cache();
$cacheContent = false;
if($checkCache !== false)
	$cacheContent = $cache->get($checkCache);
if($checkCache !== false && $cacheContent)
{
	echo $cacheContent;
}
else 
{
	if( ! isset($languages))
		$languages = null;
	if( ! isset($defaultUserRoles))
		$defaultUserRoles = null;
	ob_start();
	$dataBase['commonTables'] = $commonTables;
	foreach($imageSlices as $name=>$slice)
		$imageSlices[$name]['admin'] = Config::get('adminImageResize');
	Config::set("imageSlices", $imageSlices);
	$site = new Site($dataBase, $partConfig, Config::get('initBase'), $defaultUserRoles, $languages);
	$site->go();
	if($checkCache !== false)
		$cache->save(ob_get_contents());
	ob_end_flush();
}
?>