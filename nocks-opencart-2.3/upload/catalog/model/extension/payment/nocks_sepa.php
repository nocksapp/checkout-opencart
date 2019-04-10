<?php

require_once(__DIR__ . '/nocks/NocksPaymentMethod.php');

class ModelExtensionPaymentNocksSepa extends NocksPaymentMethod {

	protected $methodID = 'sepa';
}
