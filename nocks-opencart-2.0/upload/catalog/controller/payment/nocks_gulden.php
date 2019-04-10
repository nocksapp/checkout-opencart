<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentCatalogController.php');

class ControllerPaymentNocksGulden extends NocksPaymentCatalogController {
	protected $methodID = 'gulden';
	protected $sourceCurrency = 'NLG';

	public function getPaymentMethod() {
		return [
			'method' => 'gulden',
		];
	}
}
