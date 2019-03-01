<?
mb_internal_encoding('UTF-8');
if(count($argv) < 2)
	exit();
define('SITE_DIR', str_replace(basename(__FILE__), "", __FILE__));
set_include_path(implode(PATH_SEPARATOR, array(
	get_include_path(),
	SITE_DIR,
	SITE_DIR.'atm/',
	SITE_DIR.'lib/',
)));

$grabber_name = $argv[1];

if(!file_exists(SITE_DIR.'grabbers/'.$grabber_name.'.php'))
	exit();

require_once 'atm/Config.php';
require_once 'atm/db/MySQL.php';
require_once 'db.php';
require_once 'atm/utils/simple_html_dom.php';
require_once 'Util.php';
require_once 'pear/mime/Mime.php';
require_once 'pear/Mail.php'; 
require_once 'pear/mail/Sendmail.php';
require_once 'pear/mail/RFC822.php';
require_once 'pear/mime/MimePart.php';

$db = new DB_MySQL();

function get_grabber($site){
	global $db;
	$grabber = $db->getUniqueData("SELECT * from s_grabber where site = '$site'");
	if($grabber == null)
	{
		$grabber_id = $db->insert("INSERT INTO s_grabber (site) values('$site')");
		$grabber = $db->getUniqueData("select * from s_grabber where id = ".$grabber_id);
	}
	return $grabber;
	
}

include(SITE_DIR.'grabbers/'.$grabber_name.'.php');
?>