<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentCatalogController.php');

class ControllerExtensionPaymentNocksEthereum extends NocksPaymentCatalogController {
	protected $methodID = 'ethereum';
	protected $sourceCurrency = 'ETH';

	public function getPaymentMethod() {
		return [
			'method' => 'ethereum',
		];
	}
}
