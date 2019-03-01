<?php
require_once 'image/ImageResize.php';
Config::set('defaultLanguage', 'ru');
Config::set('superUserLogin', 'admin');
Config::set('useLogin', false);
Config::set('useTuring', false);
Config::set('initBase', true);
Config::set('userLinks', false);
Config::set('article_photo', false);
$imageSlices = array();

$dataBase = array();
Config::set('adminImageResize', new ImageResize(100, 100, ResizeFormat::DESCRIBE, true, false, false, true));


$commonTables = array();

$dataBase['image'] = <<<TABLE
    CREATE TABLE `image` (
        `id` int(11) NOT NULL auto_increment,
        `width` int(5) default 0,
        `height` int(5) default 0,
        `size` int(11) default 0,
        `mime` char(15),
        `ord` int(5) default 0,
        PRIMARY KEY  (`id`)
    ) TYPE=MyISAM CHARSET=utf8
TABLE;

$dataBase['imageresize'] = <<<TABLE
    CREATE TABLE `imageresize` (
        `id` int(11) NOT NULL auto_increment,
        `name` char(50),
        `width` int(5) default 0,
        `height` int(5) default 0,
        `size` int(11) default 0,
        `mime` char(15),
        `image` int(11) NOT NULL,
		PRIMARY KEY  (`id`)
    ) TYPE=MyISAM CHARSET=utf8
TABLE;

$dataBase['photo'] = <<<TABLE
	CREATE TABLE `photo` (
		`id` int(11) NOT NULL auto_increment,
		`image` int(11),
		`gallery` int(11),
		`description` text,
		`date` date,
		`ord` int(5) default 0,
		`temp` tinyint(1) default 1,
		`published` tinyint(1) default 0,
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM CHARSET=utf8
TABLE;

$imageSlices['photo_image'] = array('small'=>new ImageResize(100, 100, ResizeFormat::DESCRIBE, true), 'big'=>new ImageResize(400, 400, ResizeFormat::DESCRIBE, true));

$dataBase['photogallery'] = <<<TABLE
	CREATE TABLE `photogallery` (
		`id` int(11) NOT NULL auto_increment,
		`description` longtext,
		`title` text,
		`url` char(100),
		`temp` tinyint(1) default 1,
		`published` tinyint(1) default 0,
		PRIMARY KEY  (`id`),
		UNIQUE(`url`)
	) TYPE=MyISAM CHARSET=utf8
TABLE;

$dataBase['catalogcategory'] = <<<TABLE
	CREATE TABLE `catalogcategory` (
		`id` int(11) NOT NULL auto_increment,
		`description` longtext,
		`name` text,
		`url` char(100),
		`image` int(11),
		`parentid` int(11) default 0,
		`temp` tinyint(1) default 1,
		`published` tinyint(1) default 0,
		`ord` int(5) default 0,
		`code` char(200),
		`level` int(5) default 0,
		`childindex` int(7) default 0,
		PRIMARY KEY  (`id`),
		UNIQUE(`url`)
		UNIQUE(`code`)
	) TYPE=MyISAM CHARSET=utf8
TABLE;

$imageSlices['catalogcategory_image'] = array('small'=>new ImageResize(100, 100, ResizeFormat::DESCRIBE, true), 'big'=>new ImageResize(300, 300, ResizeFormat::DESCRIBE));

$dataBase['catalogitems'] = <<<TABLE
	CREATE TABLE `catalogitems` (
		`id` int(11) NOT NULL auto_increment,
		`category` int(11),
		`description` longtext,
		`short_description` text,
		`title` text,
		`url` char(100),
		`price` char(100),
		`image` int(11),
		`gallery` int(11),
		`advanced` int(11),
		`temp` tinyint(1) default 1,
		`published` tinyint(1) default 0,
		PRIMARY KEY  (`id`),
		UNIQUE(`url`)
	) TYPE=MyISAM CHARSET=utf8
TABLE;

$imageSlices['catalogitems_image'] = array('small'=>new ImageResize(100, 100, ResizeFormat::DESCRIBE, true), 'big'=>new ImageResize(300, 300, ResizeFormat::DESCRIBE), 'gallery'=>new ImageResize(100, 100, ResizeFormat::DESCRIBE, true), 'gallery_big'=>new ImageResize(300, 300, ResizeFormat::DESCRIBE, true));

$dataBase['users'] = <<<TABLE
	CREATE TABLE `users` (
		`id` int(11) NOT NULL auto_increment,
		`email` char(50) NOT NULL,
		`password` char(255) NOT NULL,
		`login` char(150) NOT NULL,
		`anketa` int(11) NOT NULL,
		`isblocked` tinyint(1) default 0,
		UNIQUE(`email`),
		UNIQUE(`login`),
		PRIMARY KEY  (`id`)
    ) TYPE=MyISAM CHARSET=utf8
TABLE;

$dataBase['userroles'] = <<<TABLE
	CREATE TABLE `userroles` (
		`id` int(11) NOT NULL auto_increment,
		`user` int(11) NOT NULL,
		`role` int(11) NOT NULL,
		PRIMARY KEY  (`id`)
	)  TYPE=MyISAM CHARSET=utf8
TABLE;

$dataBase['roles'] = <<<TABLE
	CREATE TABLE `roles` (
		`id` int(11) NOT NULL auto_increment,
		`name` char(50) NOT NULL,
		`temp` tinyint(1) default 1,
		UNIQUE(`name`),
		PRIMARY KEY  (`id`)
    ) TYPE=MyISAM CHARSET=utf8
TABLE;

$dataBase['articles'] = <<<TABLE
CREATE TABLE `articles` (
	`id` int(11) NOT NULL auto_increment,
	`parentid` int(11) default '0',
	`url` char(100),
	`otherurl` char(100) NOT NULL,
	`name` text NOT NULL,
	`title` text NOT NULL,
	`metakeys` text NOT NULL,
	`descr` text NOT NULL,
	`text` longtext NOT NULL,
	`image` int(11),
	`ord` int(5) NOT NULL default 0,
	`def` tinyint(1) default 0,
	`flag1` tinyint(1) default 0,
	`flag2` tinyint(1) default 0,
	`temp` tinyint(1) default 1,
	`published` tinyint(1) default 0,
	`code` char(200),
	`level` int(5) default 0,
	`childindex` int(7) default 0,
	`gallery` int(11),
	PRIMARY KEY  (`id`),
	UNIQUE(`url`),
	UNIQUE(`code`)
) TYPE=MyISAM CHARSET=utf8
TABLE;

$dataBase['link'] = <<<TABLE
CREATE TABLE `link` (
	`id` int(11) NOT NULL auto_increment,
	`article` int(11),
	`link` int(11),
	PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=utf8
TABLE;

$imageSlices['articles_image'] = array('small'=>new ImageResize(100, 100, ResizeFormat::DESCRIBE, true), 'big'=>new ImageResize(300, 300, ResizeFormat::DESCRIBE), 'gallery'=>new ImageResize(100, 100, ResizeFormat::DESCRIBE, true), 'gallery_big'=>new ImageResize(300, 300, ResizeFormat::DESCRIBE, true));

$dataBase['news'] = <<<TABLE
CREATE TABLE `news` (
	`id` int(11) NOT NULL auto_increment,
	`text` longtext NOT NULL,
	`shorttext` text NOT NULL,
	`title` text NOT NULL,
	`pagetitle` text NOT NULL,
	`keywords` text NOT NULL,
	`description` text NOT NULL,
	`category` int(11) NOT NULL,
	`date` date NOT NULL,
	`image` int(11),
	`temp` tinyint(1) default 1,
	`published` tinyint(1) default 0,
	`gallery` int(11),
	PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARSET=utf8
TABLE;

$imageSlices['news_image'] = array('small'=>new ImageResize(100, 100, ResizeFormat::DESCRIBE, true), 'big'=>new ImageResize(300, 300, ResizeFormat::DESCRIBE), 'gallery'=>new ImageResize(100, 100, ResizeFormat::DESCRIBE, true), 'gallery_big'=>new ImageResize(300, 300, ResizeFormat::DESCRIBE, true));

$imageSlices['news'] = array();

$dataBase['newscategory'] = <<<TABLE
CREATE TABLE `newscategory` (
	`id` int(11) NOT NULL auto_increment,
	`name` text NOT NULL,
	`url` char(100),
	`temp` tinyint(1) default 1,
	PRIMARY KEY  (`id`),
	UNIQUE(`url`)
) TYPE=MyISAM CHARSET=utf8
TABLE;

?>