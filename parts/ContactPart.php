<?php
require_once 'library/mail/Mail.php';

class ContactPart extends Part
{
	public function formMethod()
	{
		$data = parent::formMethod();
		Template::add('data', $data);
		Mail::email($this->presets->mail, 'Вопрос с сайта atmlab.ru', self::render('print/contact.phtml'), $this->presets->mail, '');
		FormController::sendData();
	}
}

?>