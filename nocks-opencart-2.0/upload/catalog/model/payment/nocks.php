<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/payment/NocksHelper.php');

class ModelPaymentNocks extends Model {

	/**
	 * Called to show the Checkout option
	 *
	 * @param array $address
	 *
	 * @return array
	 */
	public function getMethod($address) {
		$this->load->language(NocksHelper::getPath());
		$code = NocksHelper::getModuleCode();

		// Check zone
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get($code . '_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		$status = false;
		if (!$this->config->get($code . '_geo_zone_id') || $query->num_rows) {
			$status = true;
		}

		if ($status) {
			return [
				'code' => 'nocks',
				'title' => $this->language->get('checkout_title'),
				'terms' => '',
				'sort_order' => $this->config->get($code . '_sort_order'),
			];
		}

		return [];
	}
}
