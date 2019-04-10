<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/nocks/NocksPaymentAdminController.php');

class ControllerPaymentNocksGulden extends NocksPaymentAdminController
{
	protected $methodID = 'gulden';
}
