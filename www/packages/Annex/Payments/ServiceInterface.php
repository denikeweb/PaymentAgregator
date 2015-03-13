<?php
/**
 * @interface ServiceInterface
 * @package Annex\Payments
 * @author Denis Dragomirik <den@lux-blog.org>
 * @version 1.0
 * @since 13.03.2015 / 1:31
 */

namespace Annex\Payments;


interface ServiceInterface {
	public function setCurrency ($str);
	public function setDesc ($str);
	public function setOrderId ($str);
	public function setSumm ($int);
	public function setLang ($str);
	public function setEncoding ($str);
	public function setExpired ($str);
	public function setPaywayName ($str);
	public function setEmail ($str);
	public function setDebugMode ();

	public function messSuccess ();
	public function messPending ();
	public function messFail ();
	public function messRedirecting ();

	public function redirect ($return_form_code = false);
	public function setOrderObj (OrdersInterface $order);
} 