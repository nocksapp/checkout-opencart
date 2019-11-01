<?php

require_once(__DIR__ . '/nocks/NocksPaymentMethod.php');

class ModelExtensionPaymentNocksBitcoin extends NocksPaymentMethod {

	protected $methodID = 'bitcoin';
}
