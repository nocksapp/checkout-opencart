<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentAdminController.php');

class ControllerPaymentNocksSepa extends NocksPaymentAdminController
{
	protected $methodID = 'sepa';
}
