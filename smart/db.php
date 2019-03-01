<?
define('HOST','localhost');
define('DB_NAME','smart');
define('USER','root');
define('PASSWORD','1');
define('DB_PREFIX', 's_');

$config = Config::getInstance();
$db = array();

$db['grabber'] = <<<TABLE
    CREATE TABLE `grabber` (
        `id` int NOT NULL auto_increment,
        `site` char(250),
        `hash` char(50),
        `email` char(100),
        `email2` char(100),
        PRIMARY KEY  (`id`)
    ) TYPE=MyISAM CHARSET=utf8
TABLE;

$db['users'] = <<<TABLE
	CREATE TABLE `users` (
		`id` int(11) NOT NULL auto_increment,
		`email` char(50) NOT NULL,
		`password` char(255) NOT NULL,
		`login` char(150) NOT NULL,
		`anketa` int(11) NOT NULL,
		`isblocked` tinyint(1) default 0,
		`roles` char(230),
		UNIQUE(`email`),
		UNIQUE(`login`),
		PRIMARY KEY  (`id`)
    ) TYPE=MyISAM CHARSET=utf8
TABLE;


$db['anketa'] = <<<TABLE
CREATE TABLE `anketa` (
	`id` int(11) NOT NULL auto_increment,
	PRIMARY KEY  (`id`)
    ) TYPE=MyISAM CHARSET=utf8
TABLE;

$initQueries = array(
	"INSERT INTO `users` (`login`, `password`, `email`, `anketa`, `roles`) VALUES('$config->superUserLogin', '".md5("admin")."', 'admin@mail.ru', 1, '1,2,3')",
	"INSERT INTO `anketa` (user) VALUES (1)"
);

$config->initQueries = $initQueries;
$config->db = $db;
?>