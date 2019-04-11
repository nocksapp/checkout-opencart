<?php

require_once(__DIR__ . '/NocksHelper.php');
require_once(__DIR__ . '/NocksApi.php');

abstract class NocksPaymentCatalogController extends Controller {

	protected $methodID = null;
	protected $sourceCurrency = null;
	protected $path;
	protected $settingsCode;
	protected $nocksApi;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->path = NocksHelper::getPath($this->methodID);
		$this->settingsCode = NocksHelper::getModuleCode();

		$accessToken = $this->config->get($this->settingsCode . '_api_key');
		$testMode = $this->config->get($this->settingsCode . '_test_mode') === '1';
		$this->nocksApi = new NocksApi($accessToken, $testMode);
	}

	/**
	 * Should return the confirmation button
	 *
	 * @return string
	 */
	public function index() {
		$this->load->language(NocksHelper::getPath());

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['redirect_route'] = $this->path . '/start_payment';

		$prefix = NocksHelper::isOpenCart23x() ? '' : 'default/template/';
		return $this->load->view($prefix . NocksHelper::getTemplate('nocks'), array_merge($data, $this->getTemplateData()));
	}

	/**
	 * Start the payment and return the redirect uri
	 */
	public function start_payment() {
		$orderId = $this->session->data['order_id'];
		$json = [];

		if ($this->session->data['payment_method']['code'] === 'nocks_' . $this->methodID) {
			$this->load->model('checkout/order');

			$order = $this->model_checkout_order->getOrder($orderId);
			$amount = $this->currency->convert($order['total'], $this->config->get('config_currency'), 'EUR');

			$transactionData = [
				'merchant_profile' => $this->config->get($this->settingsCode . '_merchant'),
				'amount' => [
					'amount' => strval(round($amount, 2)),
					'currency' => 'EUR',
				],
				'payment_method' => $this->getPaymentMethod(),
				'redirect_url' => html_entity_decode($this->url->link($this->path . '/payment_redirect', '&order_id=' . $orderId, true)),
				'callback_url' => html_entity_decode($this->url->link($this->path . '/payment_callback', '', true)),
				'metadata' => [
					'order_id' => $orderId,
					'nocks_plugin' => 'opencart-' .  (NocksHelper::isOpenCart23x() ? '2.3' : '2.0') . ':' . NocksHelper::PLUGIN_VERSION,
					'opencart_version' => VERSION,
				],
				'description' => $orderId . ' - ' . $this->config->get('config_name'),
			];

			if ($this->sourceCurrency) {
				$transactionData['source_currency'] = $this->sourceCurrency;
			}

			$response = $this->nocksApi->createTransaction($transactionData);

			if ($response) {
				$this->model_checkout_order->addOrderHistory($orderId, $this->config->get($this->settingsCode . '_pending_status_id'));

				// Add the transactionId to the order
				$this->db->query('UPDATE `' . DB_PREFIX . 'order` SET `nocks_transaction_id` = "'
				                 . $response['data']['uuid'] . '" WHERE `order_id` = "' . $orderId . '"');

				// Build Nocks redirect url
				$json['redirect'] = $response['data']['payments']['data'][0]['metadata']['url'];
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Handle the user redirect
	 */
	public function payment_redirect() {
		$this->load->model('checkout/order');
		$this->load->language(NocksHelper::getPath());

		$orderId = $this->request->get['order_id'];
		$order = $this->model_checkout_order->getOrder($orderId);

		if ($order) {
			// Get the transaction id of the order
			$results = $this->db->query('SELECT `nocks_transaction_id` FROM `' . DB_PREFIX . 'order` WHERE `order_id` = "' . $orderId . '"');
			if ($results->num_rows) {
				// Get the transaction
				$transaction = $this->nocksApi->getTransaction($results->row['nocks_transaction_id']);
				if ($transaction) {
					if ($transaction['status'] === 'completed' || $transaction['status'] === 'open') {
						$this->response->redirect(html_entity_decode($this->url->link('checkout/success', '', true)));
						return;
					} else if ($transaction['status'] === 'cancelled') {
						// Transaction cancelled
						$this->session->data['error'] = $this->language->get('response_cancelled');
					} else {
						// Something went wrong
						$this->session->data['error'] = $this->language->get('something_went_wrong');
					}
				}
			}
		}

		$this->response->redirect(html_entity_decode($this->url->link('checkout/cart', '', true)));
	}

	/**
	 * Handle the Nocks callback
	 */
	public function payment_callback() {
		$this->load->model('checkout/order');
		$this->load->language(NocksHelper::getPath());

		$this->response->addHeader('Content-Type: application/json');
		$transactionId = file_get_contents('php://input');

		if ($transactionId) {
			// Get the transaction
			$transaction = $this->nocksApi->getTransaction($transactionId);

			if ($transaction) {
				$metadata = $transaction['metadata'];

				if (isset($metadata['order_id']) && !empty($metadata['order_id'])) {
					$orderId = $metadata['order_id'];
					$order = $this->model_checkout_order->getOrder($orderId);

					if ($order && $order['order_status_id'] === $this->config->get($this->settingsCode . '_pending_status_id')
					    && !in_array($transaction['status'], ['open', 'pending'])) {
						$notify = false;

						switch ($transaction['status']) {
							case 'cancelled';
								$statusId = $this->config->get($this->settingsCode . '_cancelled_status_id');
								$comment = $this->language->get('response_cancelled');
								break;
							case 'expired';
								$statusId = $this->config->get($this->settingsCode . '_expired_status_id');
								$comment = $this->language->get('response_expired');
								break;
							case 'completed';
								$statusId = $this->config->get($this->settingsCode . '_completed_status_id');
								$comment = $this->language->get('response_completed');
								$notify = true;
								break;
							default;
								$statusId = $this->config->get($this->settingsCode . '_failed_status_id');
								$comment = $this->language->get('response_failed');
						}

						if ($statusId) {
							$this->model_checkout_order->addOrderHistory($orderId, intval($statusId), $comment, $notify);
							$this->response->setOutput(json_encode(['success' => true]));

							return;
						}
					}
				}
			}
		}

		$this->response->setOutput(json_encode(['success' => false]));
	}

	public function getTemplateData() {
		return [];
	}

	public abstract function getPaymentMethod();
}
