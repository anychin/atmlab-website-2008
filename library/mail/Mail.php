<?
class Mail
{
	public function __construct()
	{
	}
	
	public function email($mail, $title, $message, $from, $labelFrom = "")
	{
		$headers  = "From:$labelFrom <$from>\n";
		$headers .= "Reply-To: <$from>\n";
		$headers .= "Return-path: <$from>\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/html; charset=utf-8\n";
		@mail($mail,$title,$message,$headers);
	}
}
?>