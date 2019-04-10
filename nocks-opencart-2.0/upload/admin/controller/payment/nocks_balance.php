<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentAdminController.php');

class ControllerPaymentNocksBalance extends NocksPaymentAdminController
{
	protected $methodID = 'balance';
}
