<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentAdminController.php');

class ControllerPaymentNocksIdeal extends NocksPaymentAdminController
{
	protected $methodID = 'ideal';
}
