<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentAdminController.php');

class ControllerExtensionPaymentNocksBitcoin extends NocksPaymentAdminController
{
	protected $methodID = 'bitcoin';
}
