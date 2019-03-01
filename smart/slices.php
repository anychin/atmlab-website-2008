<?
$config = Config::getInstance();

$slices = array();

$config->adminImageResize = new Image_Resize(100, 100, Image_Format::DESCRIBE, true, false, false, true);
foreach ($slices as $name=>$slice)
	$slices[$name]['admin'] = $config->adminImageResize;
$config->slices = $slices;
?>