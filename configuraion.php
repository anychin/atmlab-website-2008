<?
$languages = array("ru");

/*
define('HOST','db.atmlab.mass.hc.ru');
define('DB_NAME','wwwatmlabru_atmlab');
define('USER','atmlab_atmlab');
define('PASSWORD','idoo6Jai');
define('DB_PREFIX', 'atm_');
*/

define('HOST','localhost');
define('DB_NAME','atm');
define('USER','root');
define('PASSWORD','1');
define('DB_PREFIX', 'atm_');

$dataBase['catalogadvanced'] = <<<TABLE
	CREATE TABLE `catalogadvanced` (
		`id` int(11) NOT NULL auto_increment,
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM CHARSET=utf8
TABLE;

$dataBase['anketa'] = <<<TABLE
	CREATE TABLE `anketa` (
		`id` int(11) NOT NULL auto_increment,
		PRIMARY KEY  (`id`)
    ) TYPE=MyISAM CHARSET=utf8
TABLE;

$dataBase['projectscategory'] = <<<TABLE
	CREATE TABLE `projectscategory` (
		`id` int(11) NOT NULL auto_increment,
		`name` char(200),
		`url` char(100),
		`ord` int(5),
		`imagewidth` char(5),
		`description` text,
		`temp` tinyint(1) default 1,
		PRIMARY KEY  (`id`),
		UNIQUE(`url`)
    ) TYPE=MyISAM CHARSET=utf8
TABLE;

$dataBase['projects'] = <<<TABLE
	CREATE TABLE `projects` (
		`id` int(11) NOT NULL auto_increment,
		`category` int(11) NOT NULL,
		`name` char(200),
		`url` char(100),
		`site` char(100),
		`date` date,
		`image` int(11) NOT NULL,
		`gallery` int(11),
		`description` text,
		`shortdescription` text,
		`temp` tinyint(1) default 1,
		`published` tinyint(1) default 0,
		PRIMARY KEY  (`id`),
		UNIQUE(`url`)
    ) TYPE=MyISAM CHARSET=utf8
TABLE;

$imageSlices['projects_image'] = array('small'=>new ImageResize(56, 56, ResizeFormat::DESCRIBE, true), 'big'=>new ImageResize(300, 300, ResizeFormat::DESCRIBE_W), 'smallsmooth'=>new ImageResize(56, 56, ResizeFormat::DESCRIBE, true, false, false, false, true), 'mostbig'=>new ImageResize(700, 600, ResizeFormat::INSCRIBE));
$imageSlices['projects_photo'] = array('small'=>new ImageResize(56, 56, ResizeFormat::DESCRIBE, true), 'big'=>new ImageResize(300, 300, ResizeFormat::DESCRIBE_W), 'mostbig'=>new ImageResize(700, 600, ResizeFormat::INSCRIBE));

$dataBase['common'] = <<<TABLE
CREATE TABLE `common` (
	`id` int(11) NOT NULL auto_increment,
	`counters` longtext,
	`mail` text,
	`useeditor` tinyint(1) default 0,
	PRIMARY KEY  (`id`)
	) TYPE=MyISAM CHARSET=utf8
TABLE;

$pass = md5("admin");

$commonTables[] = "image";
$commonTables[] = "imageresize";
$commonTables[] = "anketa";
$commonTables[] = "users";
$commonTables[] = "roles";
$commonTables[] = "userroles";

Config::set("useLinks", false);
Config::set("useLogin", false);
Config::set("article_photo", false);
Config::set('cacheDir', SITE_DIR.'cache/');
Config::set('gzipDir', SITE_DIR.'cache/');
Config::set('lifeTime', 100 * 3600);
Config::set('useCache', false);
Config::set("cacheExclude", array(
));

$initQuerys = array(
	"INSERT INTO `newscategory` (`name`, `url`, `temp`) VALUES ('Категория по умолчанию', 'common', 0)",
	"INSERT INTO `users` (`login`, `password`, `email`, `anketa`) VALUES('".Config::get("superUserLogin")."', '$pass', 'admin@mail.ru', 1)",
	"INSERT INTO `roles` (`name`, `temp`) VALUES('ROLE_ADMIN', 0)",
	"INSERT INTO `roles` (`name`, `temp`) VALUES('ROLE_SUPERADMIN', 0)",
	"INSERT INTO `roles` (`name`, `temp`) VALUES('ROLE_USER', 0)",
	"INSERT INTO `userroles` (`user`, `role`) VALUES(1, 1)",
	"INSERT INTO `userroles` (`user`, `role`) VALUES(1, 2)",
	"INSERT INTO `userroles` (`user`, `role`) VALUES(1, 3)",
	"INSERT INTO `common` (`mail`, `counters`) VALUES('admin@mail.ru', '')",
	"INSERT INTO `anketa` () VALUES ()"
);
?>