<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentAdminController.php');

class ControllerExtensionPaymentNocksBalance extends NocksPaymentAdminController
{
	protected $methodID = 'balance';
}
