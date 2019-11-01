<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentAdminController.php');

class ControllerPaymentNocksBitcoin extends NocksPaymentAdminController
{
	protected $methodID = 'bitcoin';
}
