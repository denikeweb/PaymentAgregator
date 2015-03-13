<?php
/**
 * @class InterkassaService
 * @package Annex\Payments
 * @author Denis Dragomirik <den@lux-blog.org>
 * @version 1.0
 * @since 12.03.2015 / 20:32
 */

namespace Annex\Payments;


class InterkassaService extends Service {
	private $kass_id     = '54d7fdh3du8GRdfkwvodhxcy3F';
	private $ik_key      = 'd7fdhEd39jdGkodl';
	private $test_ik_key = 'd7fdhEd39jdGkodl';

	private $test_mode = false;

	public function redirect ($return_form_code = false) {
		if ( $this->validInputData() === false ) return false;

		$comment = '';
		$params = [
			'ik_co_id' => $this->kass_id,
			'ik_pm_no' => $this->getOrderId(),
			'ik_am' => $this->getSumm(),
			'ik_cur' => $this->getCurrency(),
			'ik_desc' => $this->getDesc(),
			'ik_exp' => $this->getExpired(),
			'ik_pw_via' => $this->getPaywayName(),
			'ik_loc' => $this->getLang(),
			'ik_cli' => $this->getEmail(),
			'ik_enc' => $this->getEncoding()
		];

		if ($return_form_code or $this->debug)
			$comment = '//';

		if ($this->debug)
			$this->showArray ($params);

		$html =  '<html>
			<head>
				<meta charset="utf-8">
				<title>'.$this->messRedirecting().'</title>
			</head>
			<body>'.
		$this->messRedirecting().
			'<form id="payment" name="payment" method="post" action="https://sci.interkassa.com/"
				enctype="' . $this->getEncoding() . '">'.
					'<input type="hidden" name="ik_co_id" value="'.$params ["ik_co_id"].'" />'.
					'<input type="hidden" name="ik_pm_no" value="'.$params ["ik_pm_no"].'" />'.
					'<input type="hidden" name="ik_am" value="'.$params ["ik_am"].'" />'.
					'<input type="hidden" name="ik_cur" value="'.$params ["ik_cur"].'" />'.
					'<input type="hidden" name="ik_desc" value="'.$params ["ik_desc"].'" />'.
					((!empty ($params ["ik_exp"]))?
						'<input type="hidden" name="ik_exp" value="'.$params ["ik_exp"].'" />' : '').
					((!empty ($params ["ik_pw_via"]))?
						'<input type="hidden" name="ik_pw_via" value="'.$params ["ik_pw_via"].'" />' : '').
					((!empty ($params ["ik_loc"]))?
						'<input type="hidden" name="ik_desc" value="'.$params ["ik_loc"].'" />' : '').
					((!empty ($params ["ik_cli"]))?
						'<input type="hidden" name="ik_desc" value="'.$params ["ik_cli"].'" />' : '').
					((!empty ($params ["ik_enc"]))?
						'<input type="hidden" name="ik_enc" value="'.$params ["ik_enc"].'" />' : '').
				'</form>
				<script>
					'.$comment.'document.getElementById("payment").submit ();
				</script>
			</body>
		</html>';
		return $html;
	}

	/**
	 * Input Array
	 *		(
	 *	 		[ik_co_id] => 54d7fdh3du8GRdfkwvodhxcy3F
	 *	 		[ik_co_prs_id] => 302022486128
	 *	 		[ik_inv_id] => 34231040
	 *	 		[ik_inv_st] => success
	 *	 		[ik_inv_crt] => 2015-03-13 20:24:37
	 *	 		[ik_inv_prc] => 2015-03-13 20:24:37
	 *	 		[ik_trn_id] =>
	 *	 		[ik_pm_no] => 18
	 *	 		[ik_pw_via] => test_interkassa_test_xts
	 *	 		[ik_am] => 41.48
	 *	 		[ik_co_rfn] => 41.4800
	 *	 		[ik_ps_price] => 42.73
	 *	 		[ik_cur] => UAH
	 *	 		[ik_desc] => Оплата за письмо; Pismosyl.com
	 *	 		[ik_sign] => fDeriFd4d9Fd9ksgKfxso1==
	 *		)
	 *
	 * @return mixed|void
	 */
	public function resultHandler () {
		// We can use test Interkassa paysystem
		$ik_key = ($this->test_mode) ? $this->test_ik_key : $this->ik_key;

		// set POST array and save it to log
		$dataSet = $_POST;
		$this->saveLog($dataSet);

		// get order id from input data and get order price
		$id = $dataSet['ik_pm_no'];
		$price = $this->getOrderObj()->getOrderPrice($id);

		if (!isset($dataSet ['ik_sign']))
			die ('Error: input data not exist');
		$ik_sign = $dataSet ['ik_sign'];

		unset($dataSet['ik_sign']);
		// видаляємо з даних елемент з підписом
		ksort ($dataSet, SORT_STRING);
		// сортуємо по ключам в алфавітному порядку елементи масиву
		array_push ($dataSet, $ik_key);
		// додаємо в кінець масиву "секретний ключ"
		$signString = implode (':', $dataSet);
		// конкатенуємо значення через символ ":"
		$sign = base64_encode (md5 ($signString, true));
		// беремо MD5 хеш в бінарному вигляді по
		// сформованому рядку і кодуємо в BASE64

		switch ($dataSet['ik_inv_st']) {
			case 'success' : $this->successHandler ($ik_sign == $sign, $dataSet['ik_co_rfn'], $price, $id); break;
		}
	}

	protected function validInputData () {
		$result = $this->validInputDataParent ();
		$result = (
			is_null($this->kass_id) or
			is_null($this->ik_key) or
			is_null($this->test_ik_key)
		)
			? false : $result;
		return $result;
	}
} 