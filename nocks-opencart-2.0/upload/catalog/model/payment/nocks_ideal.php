<?php

require_once(__DIR__ . '/nocks/NocksPaymentMethod.php');

class ModelPaymentNocksIdeal extends NocksPaymentMethod {

	protected $methodID = 'ideal';
}
