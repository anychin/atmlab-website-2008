<?
define('SITE_DIR', preg_replace('/\/$/', '', str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT'])).'/');
set_include_path(SITE_DIR.'/admin/parts/' . PATH_SEPARATOR . SITE_DIR.'/admin/classes/' . PATH_SEPARATOR . SITE_DIR.'/library/services/' . PATH_SEPARATOR . SITE_DIR.'/classes/services/' . PATH_SEPARATOR . SITE_DIR. '/library/' . PATH_SEPARATOR . get_include_path());

include ('../library/headers.php');
include ('../library/base-config.php');

define('SITE_TEMPLATE','site.phtml');
define('DEFAULT_PART_NAME','index');

$partConfig = array(
	'index'=>'IndexPart',
	'language'=>'LanguagePart',
	'error'=>'ErrorPart',
	'auth'=>'AuthPart',
	'news'=>'NewsPart',
	'article'=>'ArticlePart',
	'presets'=>'PresetsPart',
	'projects'=>'ProjectsPart',
	//users=>'UsersPart',
	photo=>'PhotoPart',
//	catalog=>'CatalogPart',
	'editor'=>'EditorPart',
	'compress'=>'CompressPart'
);

require_once('../configuraion.php');

Config::set('useLogin', true);
Config::set('useCache', false);

if(Config::get('useLinks')){
	$partConfig[links] = 'LinkPart';
}

require_once('../library/site.php');
?>