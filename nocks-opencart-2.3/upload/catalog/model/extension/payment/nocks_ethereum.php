<?php

require_once(__DIR__ . '/nocks/NocksPaymentMethod.php');

class ModelExtensionPaymentNocksEthereum extends NocksPaymentMethod {

	protected $methodID = 'ethereum';
}
