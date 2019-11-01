<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentCatalogController.php');

class ControllerExtensionPaymentNocksBitcoin extends NocksPaymentCatalogController {
	protected $methodID = 'bitcoin';
	protected $sourceCurrency = 'BTC';

	public function getPaymentMethod() {
		return [
			'method' => 'bitcoin',
		];
	}
}
