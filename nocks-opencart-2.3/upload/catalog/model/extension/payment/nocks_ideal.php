<?php

require_once(__DIR__ . '/nocks/NocksPaymentMethod.php');

class ModelExtensionPaymentNocksIdeal extends NocksPaymentMethod {

	protected $methodID = 'ideal';
}
