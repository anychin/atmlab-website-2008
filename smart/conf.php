<?
$config = Config::getInstance();
$config->defaultPart = 'entry';
$config->layout = 'site.phtml';
$config->superUserLogin = 'admin';
$config->useLogin = false;
$config->useTuring = false;
$config->initDB = true;
$config->not_cache_static = true;
$config->subdomains = 0;
$config->useCache = false;
$config->cacheDir = SITE_DIR.'cache/';
$config->gzipDir = SITE_DIR.'cache/';
$config->lifeTime = 100 * 3600;
$config->defaultRoles = array(Auth_Roles::USER);
$config->blocks = array();

if(!$admin_zone){
	Route_Router::add('/:part/:action/*', array('part' => $config->defaultPart, 'action' => 'index'));
	Route_Router::add('/:url', array('part' => 'articles', 'action' => 'get'), array('url' => '.+'));
}else{
	$config->defaultPart = 'index';
	$config->adminUrl = '/admin';
	$config->authClass = 'Lib_Auth';
	$config->useLogin = true;
	$config->useCache = false;
	
	
	$config->menu = array('users', 'grabber');
	
	
	Route_Router::addZone('admin');
	Route_Router::add('/:part/:action/*', array('part' => $config->defaultPart, 'action' => 'index'));
	Route_Router::add('/logout', array('part' => 'login', 'action' => 'logout'));
	Route_Router::add('/login', array('part' => 'login', 'action' => 'index'));
	Route_Router::add('/auth', array('part' => 'login', 'action' => 'form'));
	Route_Router::add('/editor/list', array('part' => 'editor', 'action' => 'l'));
}
?>