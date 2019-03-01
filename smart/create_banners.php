<?
define('SITE_DIR', str_replace(basename(__FILE__), "", __FILE__));
set_include_path(implode(PATH_SEPARATOR, array(
	get_include_path(),
	SITE_DIR,
	SITE_DIR.'atm/',
	SITE_DIR.'lib/',
)));

$depencies = array(
	'db/MySQL', 'Config', 'db', 'dao/Exp', 'dao/Order', 'dao/Field', 'dao/Dao', 'Util',
	'db/account/Dao', 'db/banner/Dao', 'db/client/Dao', 'db/company/Dao', 'db/ya/company/Dao'
);
foreach($depencies as $d) require_once $d.'.php';

$config = Config::getInstance();
//небольшое дублирование конфига
$config->path_to_keys = realpath(SITE_DIR . '../keys/') . '/';


Dao_Dao::$db = new DB_MySQL();

$f = fopen(SITE_DIR.'files/data.csv', 'r');
while(($row = fgetcsv($f, 2000, ';')) !== false)
{
	if(intval($row[0]) <= 0)
		continue;
	$ya_company_id = $row[0];
	$word = $row[3];
	$title = $row[5];
	$text = $row[6];
	$link = $row[9];
	$region = $row[10];
}


$companies = Db_Company_Dao::instance()->getCompanies();
$accounts = Db_Company_Dao::instance()->used_accounts($companies);

?>