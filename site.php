<?
set_include_path('./' . PATH_SEPARATOR . './library/' . PATH_SEPARATOR . get_include_path());
include ('library/headers.php');
include ('library/base-config.php');

define('SITE_DIR', str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/');
define('SITE_TEMPLATE', 'site.phtml');
define('DEFAULT_PART_NAME', 'articles');

$partConfig = array('articles'=>'ArticlesPart', 'error'=>'ErrorPart', 'compress'=>'CompressPart', 'news'=>'NewsPart', 'projects'=>'ProjectsPart', 'contact'=>'ContactPart', 'xml'=>'XMLPart');

require_once ('configuraion.php');
require_once ('library/site.php');
?>