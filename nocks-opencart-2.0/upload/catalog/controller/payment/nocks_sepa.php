<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentCatalogController.php');

class ControllerPaymentNocksSepa extends NocksPaymentCatalogController {
	protected $methodID = 'sepa';
	protected $sourceCurrency = 'EUR';

	public function getPaymentMethod() {
		return [
			'method' => 'sepa',
		];
	}
}
