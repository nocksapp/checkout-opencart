<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentCatalogController.php');

class ControllerPaymentNocksGulden extends NocksPaymentCatalogController {
	protected $methodID = 'litecoin';
	protected $sourceCurrency = 'LTC';

	public function getPaymentMethod() {
		return [
			'method' => 'litecoin',
		];
	}
}
