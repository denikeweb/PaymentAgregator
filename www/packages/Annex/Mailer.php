<?php
	/**
	 * @author Denis Dragomiric <den@lux-blog.org>
	 * @version Pagen 1.1.6
	 */

namespace Annex;


class Mailer {
	private $subject;
	private $text;
	private $getter;
	private $mailer;

	/**
	 * Set parameters and send message to sth e-mail
	 *
	 * @param string $getterEmail
	 * @param string $subject
	 * @param string $text
	 * @param string $mailerName
	 */
	public function facade ($getterEmail, $subject = '', $text = '', $mailerName = 'Mailer') {
		$this->text = $text;
		$this->subject = $subject;
		$this->getter = $getterEmail;
		$this->mailer = $mailerName;

		$this->sendMail();
	}

	/**
	 * Send mail by set parameters
	 *
	 * @return bool
	 */
	private function sendMail () {
		$subject = '['.$_SERVER ['SERVER_NAME'].'] '.$this->subject;
		$text = $this->text;

		/* Для отправки HTML-почты вы можете установить шапку Content-type. */
		$headers= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";

		/* дополнительные шапки */
		$headers .= "From: {$this->mailer} <no-reply@$_SERVER[SERVER_NAME]>\r\n";

		return mail($this->getter, $subject, $text, $headers);
	}
} 