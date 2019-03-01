<?
$grabber_object = get_grabber('infomiranru');

function my_callback($element) {
	$tag = $element->tag;
	if ($tag == 'a' || $tag == 'b')
		$element->outertext = $element->innertext;
	if ($tag == 'br')
		$element->outertext = '|||';
}

$delim = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";

$base_url = 'http://www.infomiran.ru/index.php?id=17&sort=date&dir=desc';
$urls = array();
for($i = 0; $i < 3; $i++)
	$urls[] = $base_url.'&p='.($i+1);

$last_date = 0;
$last_hash = '';
$data = array();

$exit = false;
foreach($urls as $url)
{
	echo $url, "\n";
	$ch = curl_init('http://www.infomiran.ru');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, 'http://www.infomiran.ru');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');
	$html = curl_exec($ch);
	curl_close($ch);
	$pos = mb_strpos($html,  $delim) + mb_strlen($delim);
	if($pos < 500)
		exit();
	$pos2 = mb_strpos($html, '</table>', $pos);
	$html = mb_substr($html, $pos, $pos2 - $pos);
	$html = str_get_html($html);
	$html->set_callback('my_callback');
	
	foreach($html->find("tr") as $i => $tr)
	{
		$tds = $tr->find("td");
		if(count($tds) < 10)
			continue;
		if(intval($tds[0]->innertext) <= 0)
			continue;
		$vals = array();
		$date = $tds[1]->innertext;
		if($last_date == 0)
			$last_date = strtotime($date);
		$vals['rooms'] = $tds[2]->innertext;
		$vals['metro'] = $tds[3]->innertext;
		$vals['square'] = $tds[4]->innertext;
		$vals['kitchen'] = $tds[5]->innertext;
		$a = explode('/', $tds[6]->innertext);
		if(count($a) < 2)
			echo $tr;
		$vals['floor'] = $a[0];
		$vals['floors'] = $a[1];
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
	$f = fopen(SITE_DIR.'data.mak', 'w');
	foreach($data as $row)
	{
		fwrite($f, "##\n");
		fwrite($f, "\n\n\n");
		fwrite($f, $row['date']."\n");//дата
		fwrite($f, "\n");//город
		fwrite($f, iconv('UTF-8', 'cp866', $row['metro'])."\n");
		fwrite($f, "\n");//удаленность
		fwrite($f, "\n");//транспорт
		fwrite($f, $row['rooms']."\n");//комнат
		fwrite($f, "\n\n");
		fwrite($f, $row['kitchen']."\n");//кухня
		fwrite($f, "\n\n");
		fwrite($f, $row['price']."\n");//цена
		fwrite($f, "\n\n\n"); 
		fwrite($f, $row['floor']."\n");//этаж
		fwrite($f, $row['floors']."\n");//этажность
		for($i = 0; $i < 16; $i++)
			fwrite($f, "\n");
		fwrite($f, '1'."\n");//телефон
		fwrite($f, "\n");
		fwrite($f, '1'."\n");//примечание к телефону
		fwrite($f, "\n");
		fwrite($f, iconv('UTF-8', 'cp866', $row['comments'])."\n");
		fwrite($f, "\n");
	}
	fclose($f);
	Util::mail($grabber_object->email, $grabber_object->email2, SITE_DIR.'data.mak', 'data.mak');
}
?>