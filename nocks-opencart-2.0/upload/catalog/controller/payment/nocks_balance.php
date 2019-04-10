<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentCatalogController.php');

class ControllerPaymentNocksBalance extends NocksPaymentCatalogController {
	protected $methodID = 'balance';

	public function getPaymentMethod() {
		return [
			'method' => 'balance',
		];
	}
}
