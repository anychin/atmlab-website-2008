<?
$grabber_object = get_grabber('infomiranruclients');

//$html = file_get_contents('http://www.infomiran.ru/index.php?id=17&sort=date&dir=desc');

function my_callback($element) {
	$tag = $element->tag;
	if ($tag == 'a' || $tag == 'b')
		$element->outertext = $element->innertext;
	if ($tag == 'br')
		$element->outertext = ' ';
}

$delim = '<table width="100%" border="0" cellspacing="0" cellpadding="2">';

$urls = array(SITE_DIR.'test.html', SITE_DIR.'test.html2');

$last_date = 0;
$last_hash = '';
$data = array();

$exit = false;
foreach($urls as $url)
{
	echo $url, "\n";
	$html = file_get_contents($url);
	$pos = mb_strpos($html,  $delim) + mb_strlen($delim);
	$pos2 = mb_strpos($html, '</table>', $pos);
	$html = mb_substr($html, $pos, $pos2 - $pos);
	$html = str_replace("\n", '', $html);
	$html = str_get_html($html);
	$html->set_callback('my_callback');
	
	foreach($html->find("tr") as $i => $tr)
	{
		$tds = $tr->find("td");
		if(count($tds) == 0)
			continue;
		$vals = array();
		$date = $tds[1]->innertext;
		if($last_date == 0)
			$last_date = strtotime($date);
		$vals['rooms'] = $tds[2]->innertext;
		$vals['metro'] = $tds[3]->innertext;
		$vals['square'] = $tds[4]->innertext;
		$vals['kitchen'] = $tds[5]->innertext;
		$vals['add'] = $tds[7]->innertext;
		$vals['price'] = $tds[8]->innertext;
		$vals['contacts'] = $tds[9]->innertext;
		$vals['comments'] = $tds[10]->innertext;
		$hash = md5(join('||||', array_values($vals)));
		if(!$grabber_object->hash)
		{
			if(strtotime($date) < $last_date)
			{
				$exit = true;
				break;
			}
		}
		else
		{
			if($hash == $grabber_object->hash)
			{
				$exit = true;
				break;
			}
		}
		if($last_hash == '')
			$last_hash = $hash;
		$vals['date'] = $date;
		$data[] = $vals;
	}
	$html->clear();
	unset($html);
	if($exit)
		break;
}

if($last_hash != "")
	$db->query("update s_grabber set hash = '$last_hash' where id = $grabber_object->id");
if($data)
{
	$f = fopen(SITE_DIR.'infomiranru_clients.txt', 'w');
	foreach($data as $row)
	{
		foreach($row as $key => $field)
			fwrite($f, $field."\n");
	}
	fclose($f);
}
var_dump($data);
echo mb_detect_encoding(file_get_contents(SITE_DIR.'16.04.10.1.mak'), 'auto');
?>