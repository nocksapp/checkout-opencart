<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentAdminController.php');

class ControllerExtensionPaymentNocksEthereum extends NocksPaymentAdminController
{
	protected $methodID = 'ethereum';
}
