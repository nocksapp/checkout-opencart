<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentCatalogController.php');

class ControllerExtensionPaymentNocksSepa extends NocksPaymentCatalogController {
	protected $methodID = 'sepa';
	protected $sourceCurrency = 'EUR';

	public function getPaymentMethod() {
		return [
			'method' => 'sepa',
		];
	}
}
