<?php
class Util
{
	public static function mail($from, $email, $file, $file_name)
	{
		$success = true;
		mb_internal_encoding('KOI8-R');
		$t = iconv('UTF-8', 'KOI8-R//TRANSLIT', 'Данные находятся во вложенном файле');
		$s = iconv('UTF-8', 'KOI8-R//TRANSLIT', 'Файл с данными');
		$mails = explode(',', $email);
		$mail = Pear_Mail::factory('Sendmail');
		$mime = new Pear_Mime_Mime();
		$mime->_build_params = array('html_charset' =>'KOI8-R', 'text_charset'=>'KOI8-R', 'head_charset'=>'KOI8-R', 'head_encoding' => 'base64', 'html_encoding' => 'KOI8-R');
		$mime->setTXTBody($t);
		$mime->addAttachment($file, 'application/octet-stream' , mb_encode_mimeheader($file_name, "KOI8-R", "B"));
		$body = $mime->get();
		$hdrs = array( 
			'From' => "$from",
			'Subject' => $s,
			'Reply-To' => $from,
			'To'=>''
		);
		for($i = 0; $i < count($mails); $i++){
			$hdrs['To'] = trim($mails[$i]);
			$hdrs = $mime->headers($hdrs);
			if($mail->send(trim($mails[$i]), $hdrs, $body) !== true)
				$success = false;
		}
		return $success;
	}
}

?>