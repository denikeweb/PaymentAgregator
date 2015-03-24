<?php
/**
 * @interface OrdersInterface
 * @package Annex\Payments
 * @author Denis Dragomirik <den@lux-blog.org>
 * @version 1.0
 * @since 12.03.2015 / 21:06
 */

namespace Annex\Payments;


interface OrdersInterface {
	public function getOrderPrice ($orderId);
	public function setOrderPaid ($orderId);
}