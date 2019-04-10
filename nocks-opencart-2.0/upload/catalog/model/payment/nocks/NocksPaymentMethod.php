<?php

require_once(__DIR__ . '/NocksHelper.php');

class NocksPaymentMethod extends Model {

	protected $methodID = null;
	protected $path;
	protected $code;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->path = NocksHelper::getPath();
		$this->code = NocksHelper::getModuleCode($this->methodID);
	}

	/**
	 * Called to show the Checkout option
	 *
	 * @param array $address
	 *
	 * @return array
	 */
	public function getMethod($address) {
		$this->load->language($this->path);

		// Check zone
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get($this->code . '_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		$status = false;
		if (!$this->config->get($this->code . '_geo_zone_id') || $query->num_rows) {
			$status = true;
		}

		if ($status) {
			return [
				'code' => 'nocks_' . $this->methodID,
				'title' => $this->language->get('checkout_title_' . $this->methodID),
				'terms' => '',
				'sort_order' => $this->config->get($this->code . '_sort_order'),
			];
		}

		return [];
	}
}
