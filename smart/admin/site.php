<?
mb_internal_encoding('UTF-8');
set_include_path(implode(PATH_SEPARATOR, array(get_include_path(), '../atm', '../lib')));
define('SITE_DIR', str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/');
require_once 'loader/Loader.php';
$admin_zone = true;
include '../conf.php';
include '../db.php';
include '../slices.php';
$site = new Lib_Site();
$site->go();
?>