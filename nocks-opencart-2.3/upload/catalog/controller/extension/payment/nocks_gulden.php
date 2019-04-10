<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentCatalogController.php');

class ControllerExtensionPaymentNocksGulden extends NocksPaymentCatalogController {
	protected $methodID = 'gulden';
	protected $sourceCurrency = 'NLG';

	public function getPaymentMethod() {
		return [
			'method' => 'gulden',
		];
	}
}
