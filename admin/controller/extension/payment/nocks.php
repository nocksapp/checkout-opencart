<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/controller/extension/payment/nocks/helper.php');
require_once(dirname(DIR_SYSTEM) . '/catalog/controller/extension/payment/nocks/api.php');


class ControllerExtensionPaymentNocks extends Controller
{
	protected $errors = [];
	protected $data = [];

	/**
	 * On install add the transaction_id column to
	 *
	 * @return void
	 */
	public function install () {
		$this->db->query('ALTER TABLE `' . DB_PREFIX . 'order` ADD `nocks_transaction_id` VARCHAR(255) DEFAULT NULL;');

		// Install extension
		$this->load->model('extension/extension');
		$this->model_extension_extension->install('payment', 'nocks');

		// Add permissions
		$userId = $this->getUserId();
		$this->model_user_user_group->addPermission($userId, 'access', 'extension/payment/nocks');
		$this->model_user_user_group->addPermission($userId, 'modify', 'extension/payment/nocks');
	}

	/**
	 * Uninstall extension
	 *
	 * @return void
	 */
	public function uninstall () {
		$this->load->model('extension/extension');
		$this->model_extension_extension->uninstall('payment', 'nocks');
	}

	/**
	 * Render the payment method's settings page.
	 */
	public function index () {
		// Load essential models
		$this->load->language('extension/payment/nocks');
		$this->load->model('setting/setting');
		$this->load->model('setting/store');
		$this->load->model('localisation/geo_zone');
		$this->load->model('localisation/order_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$shops = $this->getMultiStores();
		$code = NocksHelper::MODULE_CODE;
		$this->setMultiStoresData($shops);

		// Call validate method on POST
		if ($this->request->server['REQUEST_METHOD'] === 'POST') {
			$doRedirect = false;
			foreach($shops as $store) {
				if ($this->validate($store['id'])) {
					$this->model_setting_setting->editSetting($code, $this->request->post['stores'][$store['id']], $store['id']);

					$doRedirect = true;
				}
			}

			if ($doRedirect) {
				$this->session->data['success'] = $this->language->get('successfully_saved');
				$this->response->redirect(html_entity_decode($this->url->link($this->getExtensionsUri(), array_merge(['type' => 'payment'], $this->getTokenParams()), true)));
			}
		}

		// Set data for template
		$data['heading_title'] = $this->language->get('heading_title');
		$data['nocks_config_title'] = $this->language->get('nocks_config_title');
		$data['main_config_title'] = $this->language->get('main_config_title');
		$data['status_config_title'] = $this->language->get('status_config_title');
		$data['about_title'] = $this->language->get('about_title');

		$data['entry_api_key'] = $this->language->get('api_key');
		$data['entry_merchant'] = $this->language->get('merchant');
		$data['entry_payment'] = $this->language->get('payment');
		$data['entry_geo_zone'] = $this->language->get('geo_zone');
		$data['entry_status'] = $this->language->get('status');
		$data['entry_sort_order'] = $this->language->get('sort_order');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		$data['failed_status'] = $this->language->get('failed_status');
		$data['cancelled_status'] = $this->language->get('cancelled_status');
		$data['pending_status'] = $this->language->get('pending_status');
		$data['expired_status'] = $this->language->get('expired_status');
		$data['completed_status'] = $this->language->get('completed_status');

		$data['get_merchants_url'] = html_entity_decode($this->url->link('extension/payment/nocks/get_merchants', $this->getTokenParams(), true));

		$data['button_save']            = $this->language->get("button_save");
		$data['button_cancel']          = $this->language->get("button_cancel");

		$data['shops'] = $shops;
		$data['code'] = $code;

		// If there are errors, show the errors.
		$data['error_warning'] = isset($this->errors['warning']) ? $this->errors['warning'] : '';

		foreach($shops as $store) {
			if (isset($this->errors[$store['id']]['api_key'])) {
				$data['stores'][$store['id']]['error_api_key'] = $this->errors[$store['id']]['api_key'];
			} else {
				$data['stores'][$store['id']]['error_api_key'] = '';
			}

			if (isset($this->errors[$store['id']]['total'])) {
				$data['stores'][$store['id']]['error_total'] = $this->errors[$store['id']]['total'];
			} else {
				$data['stores'][$store['id']]['error_total'] = '';
			}
		}

		// Setup breadcrumbs
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'href'      => html_entity_decode($this->url->link('common/dashboard', $this->getTokenParams(), true)),
			'text'      => $this->language->get('text_home'),
			'separator' => false,
		];

		$data['breadcrumbs'][] = [
			'href'      => html_entity_decode($this->url->link($this->getExtensionsUri(), array_merge(['type' => 'payment'], $this->getTokenParams()), true)),
			'text'      => $this->language->get('payment'),
			'separator' => ' :: ',
		];

		$data['breadcrumbs'][] = [
			'href'     => html_entity_decode($this->url->link('extension/payment/nocks', $this->getTokenParams(), true)),
			'text'      => $this->language->get('heading_title'),
			'separator' => ' :: ',
		];

		// Form action url
		$data['action'] = html_entity_decode($this->url->link('extension/payment/nocks', $this->getTokenParams(), true));
		$data['cancel'] = html_entity_decode($this->url->link($this->getExtensionsUri(), array_merge(['type' => 'payment'], $this->getTokenParams()), true));

		// Default settings
		$settings = [
			$code . '_api_key' => null,
			$code . '_merchant' => null,
			$code . '_pending_status_id' => 1,
			$code . '_failed_status_id' => 10,
			$code . '_cancelled_status_id' => 7,
			$code . '_expired_status_id' => 14,
			$code . '_completed_status_id' => 2,
			$code . '_geo_zone_id' => null,
			$code . '_status' => false,
			$code . '_sort_order' => 1,
		];

		foreach($shops as $store) {
			$dbSettings = $this->model_setting_setting->getSetting($code, $store['id']);

			foreach ($settings as $key => $value) {
				if (isset($this->request->post['stores'][$store['id']][$key])) {
					// Get the value from the POST request
					$data['stores'][$store['id']][$key] = $this->request->post['stores'][$store['id']][$key];
				} else {
					// Get the value from the db or use the default value
					$data['stores'][$store['id']][$key] = isset($dbSettings[$key]) ? $dbSettings[$key] : $value;
				}
			}
		}

		$this->renderTemplate('extension/payment/nocks', $data, ['header', 'column_left', 'footer']);
	}

	private function getMerchantOptions($accessToken) {
		// Retrieve the merchants by the key
		$api = new NocksApi($accessToken);
		$merchants = $api->getMerchants();

		if (!$merchants) {
			return null;
		}

		// Build merchants options
		$options = [];
		foreach ($merchants as $merchant) {
			$merchantName = $merchant['name'];
			foreach ($merchant['merchant_profiles']['data'] as $profile) {
				$label = ($merchantName === $profile['name'] ? $merchantName : $merchantName . ' (' . $profile['name'] . ')')
				         . ' (' . $merchant['coc'] . ')';

				$options[] = [
					'value' => $profile['uuid'],
					'label' => htmlentities($label, ENT_COMPAT, 'UTF-8'),
				];
			}
		}

		return $options;
	}

	public function get_merchants() {
		$this->response->addHeader('Content-Type: application/json');

		// Check key is given in request
		if (empty($this->request->get['key'])) {
			$this->response->setOutput(json_encode([
				'success' => false,
				'message' => 'Invalid api key',
			]));

			return;
		}

		$merchants = $this->getMerchantOptions($this->request->get['key']);

		if (!$merchants) {
			$this->response->setOutput(json_encode([
				'success' => false,
				'message' => 'Invalid api key',
			]));

			return;
		}

		$this->response->setOutput(json_encode([
			'success' => true,
			'merchants' => $merchants,
		]));
	}

	/**
	 * Validate settings post
	 *
	 * @param int $store The store id
	 * @return bool
	 */
	private function validate ($store = 0) {
		$code = NocksHelper::MODULE_CODE;

		if (!$this->user->hasPermission('modify', 'extension/payment/nocks')) {
			$this->errors['warning'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->post['stores'][$store][$code . '_api_key'])
			|| !$this->request->post['stores'][$store][$code . '_api_key']) {
			$this->errors[$store]['api_key'] = $this->language->get('error_api_key');
		} else {
			$merchantOptions = $this->getMerchantOptions($this->request->post['stores'][$store][$code . '_api_key']);
			if (!$merchantOptions) {
				// Invalid api key
				$this->errors[$store]['api_key'] = $this->language->get('error_api_key');
			} else {
				$merchantIds = array_map(function($option) {
					return $option['value'];
				}, $merchantOptions);

				// Check the merchant is set
				if (!isset($this->request->post['stores'][$store][$code . '_merchant'])
				    || !$this->request->post['stores'][$store][$code . '_merchant']
				    || !in_array($this->request->post['stores'][$store][$code . '_merchant'], $merchantIds)) {

					$this->errors[$store]['merchant'] = $this->language->get('error_merchant');
				}
			}
		}

		return (count($this->errors) == 0);
	}

	/**
	 * Map template handling for different Opencart versions
	 *
	 * @param string $template
	 * @param array $data
	 * @param array $common_children
	 *
	 * @return string
	 */
	protected function renderTemplate($template, $data, $common_children = []) {
		if (!NocksHelper::isOpenCart3x()) {
			$template .= '.tpl';
		}

		foreach ($common_children as $child) {
			$data[$child] = $this->load->controller('common/' . $child);
		}

		return $this->response->setOutput($this->load->view($template, $data));
	}

	/**
	 * Retrieve additional store id's from store table.
	 * Will not include default store. Only the additional stores. So we inject the default store here.
	 * @return array
	 */
	protected function getMultiStores()
	{
		$sql = $this->db->query(sprintf("SELECT store_id as id, name FROM %sstore", DB_PREFIX));
		$rows = $sql->rows;
		$default = array(
			array(
				'id' => 0,
				'name' => $this->config->get('config_name')
			)
		);
		$allStores = array_merge($default, $rows);

		return $allStores;
	}

	/**
	 * Set multi store data
	 *
	 * @param $shops
	 */
	protected function setMultiStoresData($shops) {
		foreach($shops as $store) {
			$sql = $this->db->query(sprintf("SELECT * FROM %ssetting WHERE store_id = %s", DB_PREFIX, $store['id']));
			$rows = $sql->rows;
			$newArrray = array();
			foreach($rows as $setting) {
				$newArrray[$setting['key']] = $setting['value'];
			}

			$this->data['stores'][$store['id']] = $newArrray;
		}
	}

	/**
	 * Get the extension installation handler.
	 *
	 * @return Model
	 */
	protected function getExtensionModel() {
		$this->load->model('extension/extension');
		return $this->model_extension_extension;
	}

	/**
	 * @return string
	 */
	private function getExtensionsUri() {
		if (NocksHelper::isOpenCart3x()) {
			return 'marketplace/extension';
		}

		if (NocksHelper::isOpenCart23x()) {
			return 'extension/extension';
		}

		return 'extension/payment';
	}

	/**
	 * @return array
	 */
	private function getTokenParams() {
		if (NocksHelper::isOpenCart3x()) {
			return ['user_token' => $this->session->data['user_token']];
		}

		return ['token' => $this->session->data['token']];
	}

	private function getUserId() {
		$this->load->model('user/user_group');

		if (method_exists($this->user, 'getGroupId')) {
			return $this->user->getGroupId();
		}

		return $this->user->getId();
	}
}
