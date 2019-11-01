<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentAdminController.php');

class ControllerExtensionPaymentNocksLitecoin extends NocksPaymentAdminController
{
	protected $methodID = 'litecoin';
}
