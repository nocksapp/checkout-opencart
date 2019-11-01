<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentAdminController.php');

class ControllerPaymentNocksLitecoin extends NocksPaymentAdminController
{
	protected $methodID = 'litecoin';
}
