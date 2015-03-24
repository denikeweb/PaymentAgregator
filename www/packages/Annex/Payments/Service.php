<?php
/**
 * @class Service
 * @package Annex\Payments
 * @author Denis Dragomirik <den@lux-blog.org>
 * @version 1.0
 * @since 12.03.2015 / 19:23
 */

namespace Annex\Payments;

/**
 * Class Service
 *
 * @package Annex\Payments
 */
abstract class Service implements ServiceInterface {
	/**
	 * list for usage at child classes:
	 *
	 * @see showArray ($array);
	 * @see saveLog ();
	 * @see defineUserLang ($array);
	 *
	 * @see validInputData ();
	 * @see validInputDataParent ();
	 *
	 * @see successHandler ($valid, $payed, $price, $id);
	 */


	private $desc;
	private $summ;
	private $order_id;
	private $encoding = "utf-8";
	private $currency = "USD";
	private $lang;
	private $payway_name;
	private $expired;
	private $email;

	/**
	 * Payment will be redirected to
	 * payment service only if var false
	 *
	 * @var bool
	 */
	protected $debug = false;

	/**
	 * OrdersInterface obj
	 *
	 * @var OrdersInterface
	 */
	private $order;

	/**
	 * @param bool $return_form_code
	 *
	 * @return string
	 */
	abstract function redirect ($return_form_code = false);

	/**
	 * @return mixed
	 */
	abstract function resultHandler ();

	/**
	 * @param string $desc
	 */
	function setDesc ($desc) {
		$this->desc = (string) $desc;
	}

	/**
	 * @param Orders $order
	 */
	function setOrderObj (OrdersInterface $order) {
		$this->order = $order;
	}

	/**
	 * @param float $summ
	 */
	function setSumm ($summ) {
		$this->summ = (float) $summ;
	}

	/**
	 * @param int $order_id
	 */
	function setOrderId ($order_id) {
		$this->order_id = (int) $order_id;
	}

	/**
	 * @param string $order_id
	 */
	function setEncoding ($encoding) {
		$this->encoding = (string) $encoding;
	}

	/**
	 * @param string $order_id
	 */
	function setCurrency ($currency) {
		$this->currency = (string) $currency;
	}

	/**
	 * @param string <2011-10-01 20:50:33> $expired
	 */
	function setExpired ($expired) {
		$this->expired = (string) $expired;
	}

	/**
	 * @param string $payway_name
	 */
	function setPaywayName ($payway_name) {
		$this->payway_name = (string) $payway_name;
	}
	/**
	 * @param string $payway_name
	 */
	function setEmail ($email) {
		$this->email = (string) $email;
	}

	/**
	 * Set debug mode
	 */
	function setDebugMode () {
		$this->debug = true;
	}

	/**
	 * @param string $lang
	 */
	function setLang ($lang) {
		$this->lang = (string) $lang;
	}

	/**
	 * @return float
	 */
	function getSumm () {
		return $this->summ;
	}

	/**
	 * @return int
	 */
	function getOrderId () {
		return $this->order_id;
	}

	/**
	 * @return string
	 */
	function getEncoding () {
		return $this->encoding;
	}

	/**
	 * @return OrdersInterface
	 */
	function getOrderObj () {
		return $this->order;
	}

	/**
	 * @return string
	 */
	function getCurrency () {
		return $this->currency;
	}

	/**
	 * @return string
	 */
	function getDesc () {
		return $this->desc;
	}

	/**
	 * @return string
	 */
	function getLang () {
		return $this->lang;
	}

	/**
	 * @return string
	 */
	function getExpired () {
		return $this->expired;
	}

	/**
	 * @return string
	 */
	function getPaywayName () {
		return $this->payway_name;
	}

	/**
	 * @return string
	 */
	function getEmail () {
		return $this->email;
	}


	/**
	 * You can override this method
	 * to set needed functions after object construct
	 *
	 * Event called at constructor
	 *
	 * @see __construct
	 */
	protected function afterConstruct () {}

	/**
	 * Constructor, set default language
	 * You can override method afterConstruct
	 * to set needed functions after object construct
	 *
	 * @final
	 * @see afterConstruct
	 * @see defineUserLang
	 */
	final public function __construct () {
		$this->setLang((string) $this->defineUserLang());
		$this->afterConstruct();
	}

	/**
	 * Check is all needed data setted
	 *
	 * @see validInputDataParent
	 * @return bool
	 */
	protected function validInputData () {
		$result = $this->validInputDataParent ();
		return $result;
	}

	/**
	 * @return bool
	 */
	protected function validInputDataParent () {
		$result = (
			is_null($this->summ) or
			is_null($this->desc) or
			is_null($this->order_id)
		)
			? false : true;
		return $result;
	}

	/**
	 * @return string
	 */
	function messSuccess () {
		return 'You have payed payment';
	}

	/**
	 * @return string
	 */
	function messPending () {
		return 'Your payment at pending';
	}

	/**
	 * @return string
	 */
	function messFail () {
		return 'You have refused payment.';
	}

	/**
	 * @return string
	 */
	function messRedirecting () {
		return 'Redirecting to payment page...';
	}

	/**
	 * Print array with tabulation
	 *
	 * @param $array
	 */
	protected function showArray ($array) {
		ob_start();
		echo '<pre style="white-space: pre-wrap; overflow: hidden; text-align: left;"><code class="php">';
		print_r($array);
		echo '</code></pre>';
		$content = ob_get_clean();
		return$content;
	}

	/**
	 * Function define user language
	 *
	 * @return string
	 */
	protected function defineUserLang () {
		$input = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
		// sth like 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3'
		preg_match_all ('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', strtolower ($input), $matches);
		$langs = array_combine ($matches[2], $matches[1]);
		// create array of languages with their weight

		arsort ($langs);
		// sort by q

		$default_lang = explode ('-', array_shift ($langs)) [0];
		// get element with max weight
		// it can be 'en-us', so we get first part - 'en'

		return $default_lang;
	}

	/**
	 * @abstract
	 * @return bool
	 */
	protected function saveLog (array $array) {
		ob_start();
			print_r($array);
		$content = ob_get_clean();
	}

	/**
	 * @param bool  $valid
	 * @param float $payed
	 * @param float $price
	 * @param int   $id
	 */
	protected function successHandler ($valid, $payed, $price, $id) {
		$correctSumm = $payed > $price * 0.99;
		if ($valid && $correctSumm)
			$this->getOrderObj()->setOrderPaid ($id);
			// change order status
		else
			die  ('Error: faulty input data');
	}
} 