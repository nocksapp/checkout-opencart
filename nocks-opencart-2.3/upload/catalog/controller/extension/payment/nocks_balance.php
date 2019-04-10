<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentCatalogController.php');

class ControllerExtensionPaymentNocksBalance extends NocksPaymentCatalogController {
	protected $methodID = 'balance';

	public function getPaymentMethod() {
		return [
			'method' => 'balance',
		];
	}
}
