<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentCatalogController.php');

class ControllerPaymentNocksBitcoin extends NocksPaymentCatalogController {
	protected $methodID = 'bitcoin';
	protected $sourceCurrency = 'BTC';

	public function getPaymentMethod() {
		return [
			'method' => 'bitcoin',
		];
	}
}
