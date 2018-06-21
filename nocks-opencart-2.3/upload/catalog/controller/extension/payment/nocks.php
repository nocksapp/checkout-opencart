<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/NocksHelper.php');
require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/NocksApi.php');


class ControllerExtensionPaymentNocks extends Controller {

	/**
	 * Should return the confirmation button
	 *
	 * @return string
	 */
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['redirect_route'] = NocksHelper::getPath('nocks/start_payment');

		$prefix = NocksHelper::isOpenCart23x() ? '' : 'default/template/';
		return $this->load->view($prefix . NocksHelper::getTemplate('nocks'), $data);
	}

	/**
	 * Start the payment and return the redirect uri
	 */
	public function start_payment() {
		$code = NocksHelper::getModuleCode();
		$accessToken = $this->config->get($code . '_api_key');
		$testMode = $this->config->get($code . '_test_mode') === '1';
		$orderId = $this->session->data['order_id'];
		$json = [];

		if ($this->session->data['payment_method']['code'] === 'nocks' && $accessToken) {
			$this->load->model('checkout/order');

			$order = $this->model_checkout_order->getOrder($orderId);
			$amount = $this->currency->convert($order['total'], $this->config->get('config_currency'), 'EUR');

			$api = new NocksApi($accessToken, $testMode);
			$response = $api->createTransaction([
				'merchant_profile' => $this->config->get($code . '_merchant'),
				'source_currency' => 'NLG',
				'amount' => [
					'amount' => strval(round($amount, 2)),
					'currency' => 'EUR',
				],
				'redirect_url' => html_entity_decode($this->url->link(NocksHelper::getPath('nocks/payment_redirect'), '&order_id=' . $orderId, true)),
				'callback_url' => html_entity_decode($this->url->link(NocksHelper::getPath('nocks/payment_callback'), '', true)),
				'metadata' => [
					'order_id' => $orderId,
				],
			]);

			if ($response) {
				$this->model_checkout_order->addOrderHistory($orderId, $this->config->get($code . '_pending_status_id'));

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

		$code = NocksHelper::getModuleCode();
		$accessToken = $this->config->get($code . '_api_key');
		$testMode = $this->config->get($code . '_test_mode') === '1';

		$orderId = $this->request->get['order_id'];
		$order = $this->model_checkout_order->getOrder($orderId);

		if ($order && $accessToken) {
			// Get the transaction id of the order
			$results = $this->db->query('SELECT `nocks_transaction_id` FROM `' . DB_PREFIX . 'order` WHERE `order_id` = "' . $orderId . '"');
			if ($results->num_rows) {
				// Get the transaction
				$api = new NocksApi($accessToken, $testMode);
				$transaction = $api->getTransaction($results->row['nocks_transaction_id']);
				if ($transaction) {
					if ($transaction['status'] === 'completed') {
						// Transaction completed, redirect to success
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

		$code = NocksHelper::getModuleCode();
		$accessToken = $this->config->get($code . '_api_key');
		$testMode = $this->config->get($code . '_test_mode') === '1';

		$this->response->addHeader('Content-Type: application/json');
		$transactionId = file_get_contents('php://input');

		if ($transactionId && $accessToken) {
			// Get the transaction
			$api = new NocksApi($accessToken, $testMode);
			$transaction = $api->getTransaction($transactionId);

			if ($transaction) {
				$metadata = $transaction['metadata'];

				if (isset($metadata['order_id']) && !empty($metadata['order_id'])) {
					$orderId = $metadata['order_id'];
					$order = $this->model_checkout_order->getOrder($orderId);

					if ($order && $order['order_status_id'] === $this->config->get($code . '_pending_status_id')
						&& !in_array($transaction['status'], ['open', 'pending'])) {
						$notify = false;

						switch ($transaction['status']) {
							case 'cancelled';
								$statusId = $this->config->get($code . '_cancelled_status_id');
								$comment = $this->language->get('response_cancelled');
								break;
							case 'expired';
								$statusId = $this->config->get($code . '_expired_status_id');
								$comment = $this->language->get('response_expired');
								break;
							case 'completed';
								$statusId = $this->config->get($code . '_completed_status_id');
								$comment = $this->language->get('response_completed');
								$notify = true;
								break;
							default;
								$statusId = $this->config->get($code . '_failed_status_id');
								$comment = $this->language->get('response_failed');
						}

						if ($statusId) {
							$this->model_checkout_order->addOrderHistory( $orderId, intval($statusId), $comment, $notify);
							$this->response->setOutput(json_encode(['success' => true]));

							return;
						}
					}
				}
			}
		}

		$this->response->setOutput(json_encode(['success' => false]));
	}
}
