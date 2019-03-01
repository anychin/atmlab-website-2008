<?php
$base = $_SERVER['DOCUMENT_ROOT'].'/';
$dirs = array('library', 'classes', 'parts');
$scripts = array();
function processDir($d){
	global $scripts;
	$h = opendir($d);
	while (false !== ($file = readdir($h)))
	{
		if($file == "." || $file == "..")
			continue;
		if(is_dir($d."/".$file))
			processDir($d."/".$file);
		else if(preg_match('/\\.php$/', $file) && $file != 'site.php')
			$scripts[] = $d."/".$file;
	}
    closedir($h);
}

for($i = 0; $i < count($dirs); $i++)
	processDir($base.$dirs[$i]);

$str = "";
for($i = 0; $i < count($scripts); $i++)
	$str .= file_get_contents($scripts[$i]);
	
echo count($scripts);

function cleanfile($file){
	return cleanstr(file_get_contents($file));
}

function cleanstr($str){
	return preg_replace('/\\s(require|include)[^;]+\\.php[^;]+;/', '', $str);
}

$str = cleanstr($str);

$main_file = cleanfile($base.'site.php');

$all = $str . $main_file . cleanfile($base.'configuraion.php') . cleanfile($base.'library/site.php');

$all = preg_replace('/\\?>(\n)*<\\?php/', '', $all);
$all = preg_replace('/\\?>(\n)*<\\?/', '', $all);

file_put_contents('cache/min.php', $all);
?>