<?php

require_once(__DIR__ . '/nocks/NocksPaymentMethod.php');

class ModelExtensionPaymentNocksBalance extends NocksPaymentMethod {

	protected $methodID = 'balance';
}
