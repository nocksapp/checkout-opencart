<?php

require_once(__DIR__ . '/nocks/NocksPaymentMethod.php');

class ModelPaymentNocksSepa extends NocksPaymentMethod {

	protected $methodID = 'sepa';
}
