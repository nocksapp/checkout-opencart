<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/nocks/NocksPaymentAdminController.php');

class ControllerExtensionPaymentNocksGulden extends NocksPaymentAdminController
{
	protected $methodID = 'gulden';
}
