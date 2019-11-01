<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentCatalogController.php');

class ControllerExtensionPaymentNocksLitecoin extends NocksPaymentCatalogController {
	protected $methodID = 'litecoin';
	protected $sourceCurrency = 'LTC';

	public function getPaymentMethod() {
		return [
			'method' => 'litecoin',
		];
	}
}
