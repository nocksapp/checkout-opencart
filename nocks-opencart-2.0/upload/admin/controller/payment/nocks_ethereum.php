<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentAdminController.php');

class ControllerPaymentNocksEthereum extends NocksPaymentAdminController
{
	protected $methodID = 'ethereum';
}
