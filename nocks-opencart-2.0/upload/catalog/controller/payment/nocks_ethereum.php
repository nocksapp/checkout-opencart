<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentCatalogController.php');

class ControllerPaymentNocksGulden extends NocksPaymentCatalogController {
	protected $methodID = 'ethereum';
	protected $sourceCurrency = 'ETH';

	public function getPaymentMethod() {
		return [
			'method' => 'ethereum',
		];
	}
}
