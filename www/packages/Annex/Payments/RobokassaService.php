<?php
/**
 * @class RobokassaService
 * @package Annex\Payments
 * @author Denis Dragomirik <den@lux-blog.org>
 * @version 1.0
 * @since 12.03.2015 / 20:32
 */

namespace Annex\Payments;


class RobokassaService extends Service {
	private $login = 'demo';
	private $pass = 'password_1';

	public function redirect ($return_form_code = false) {
		if ( $this->validInputData() === false ) return false;

		// Payment of the set sum with a choice of currency on site ROBOKASSA

		// registration info (login, password #1)
		$mrh_login = $this->login;
		$mrh_pass  = $this->pass;

		// number of order
		$inv_id = $this->getOrderId();

		// sum of order
		$out_summ = $this->getSumm();

		// code of goods
		$shp_item = "1";

		// generate signature
		$crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass:Shp_item=$shp_item");
		$comment = '';
		$params = [
			'mrh_login' => $mrh_login,
			'out_summ ' => $out_summ,
			'inv_id'    => $inv_id,
			'inv_desc'  => $this->getDesc(),
			'crc'       => $crc,
			'shp_item'  => $shp_item,
			'in_curr'   => $this->getCurrency(),
			'culture'   => $this->getLang() // language
		];

		if ($return_form_code or $this->debug)
			$comment = '//';


		$html =  '
				<meta charset="utf-8">'.
				$this->messRedirecting().
				'<form id="payment" name="payment" method="post" action="https://merchant.roboxchange.com/Index.aspx">
					<input type="hidden" name="MrchLogin"         value="' . $params ['mrh_login'] .'"    >
					<input type="hidden" name="OutSum"            value="' . $params ['out_summ'] . '"    >
					<input type="hidden" name="InvId"             value="' . $params ['inv_id'] .   '"      >
				    <input type="hidden" name="Desc"              value="' . $params ['inv_desc'] . '"     >
				    <input type="hidden" name="SignatureValue"    value="' . $params ['crc'] .      '"         >
				    <input type="hidden" name="Shp_item"          value="' . $params ['shp_item'] . '"    >
				    <input type="hidden" name="IncCurrLabel"      value="' . $params ['in_curr'] .  '"      >
				    <input type="hidden" name="Culture"           value="' . $params ['culture'] .  '"      >
			    </form>

				<script>
					'.$comment.'document.getElementById("payment").submit ();
				</script>';
		if ($this->debug)
			$html .= $this->showArray ($params);
		return $html;
	}

	public function resultHandler () {
		// registration info (password #1)
		$mrh_pass = $this->pass;

		// set POST array and save it to log
		$dataSet = $_REQUEST;
		$this->saveLog($dataSet);

		// read parameters
		$out_summ = $dataSet ["OutSum"];
		$id = $dataSet ["InvId"];
		$shp_item = $dataSet ["Shp_item"];
		$crc = $dataSet ["SignatureValue"];

		$crc = strtoupper($crc);
		$my_crc = strtoupper(md5("$out_summ:$id:$mrh_pass:Shp_item=$shp_item"));

		// get order id from input data and get order price
		$price = $this->getOrderObj()->getOrderPrice($id);

		switch ($dataSet['ik_inv_st']) {
			case 'success' : $this->successHandler ($my_crc == $crc, $out_summ, $price, $id); break;
		}
	}

	protected function validInputData () {
		$result = $this->validInputDataParent ();
		$result = (
			is_null($this->login) or
			is_null($this->pass)
		)
			? false : $result;
		return $result;
	}

	function messFail () {
		$inv_id = $_REQUEST["InvId"];
		return "You have refused payment. Order# $inv_id\n";
	}

	function messRedirecting () {
		return 'Идет перенаправление на страницу оплаты...';
	}
} 