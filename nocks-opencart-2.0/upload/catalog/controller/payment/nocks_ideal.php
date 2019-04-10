<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentCatalogController.php');
require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksApi.php');

class ControllerPaymentNocksIdeal extends NocksPaymentCatalogController {
	protected $methodID = 'ideal';
	protected $sourceCurrency = 'EUR';

	public function getTemplateData() {
		// Load issuers
		$issuers = $this->nocksApi->getIdealIssuers();

		return [
			'text_select_your_bank' => $this->language->get('text_select_your_bank'),
			'issuers' => $issuers,
		];
	}

	public function getPaymentMethod() {
		$paymentMethod = ['method' => 'ideal'];

		if (isset($this->request->get['issuer']) && !empty($this->request->get['issuer'])) {
			$paymentMethod['metadata'] = ['issuer' => $this->request->get['issuer']];
		}

		return $paymentMethod;
	}
}
