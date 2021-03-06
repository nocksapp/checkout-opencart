<?php


class NocksApi {
	protected $url;
	protected $testMode;
	protected $accessToken;

	public function __construct($accessToken, $testMode = false) {
		$this->url = $testMode ? 'https://sandbox.nocks.com/api/v2/' : 'https://api.nocks.com/api/v2/';
		$this->testMode = $testMode;
		$this->accessToken = $accessToken;
	}

	public function isTestMode() {
		return $this->testMode;
	}

	/**
	 * Get the scopes
	 *
	 * @return array
	 */
	public function getScopes() {
		$apiUrl = $this->url;
		$this->url = $this->testMode ? 'https://sandbox.nocks.com/oauth/' : 'https://www.nocks.com/oauth/';

		$response = $this->call('token-scopes', null);
		$this->url = $apiUrl;

		if ($response === null) {
			return [];
		}

		return array_map(function($scope) {
			return $scope['id'];
		}, $response);
	}

	/**
	 * Get the merchants
	 *
	 * @return bool|array
	 */
	public function getMerchants() {
		$response = $this->call('merchant', null);

		if ($response) {
			return $response['data'];
		}

		return false;
	}

	/**
	 * Create a new transaction
	 *
	 * @param $data
	 *
	 * @return bool|mixed|null
	 */
	public function createTransaction($data) {
		$response = $this->call('transaction', $data);

		if ($response) {
			return $response;
		}

		return false;
	}

	/**
	 * Get a transaction by id
	 *
	 * @param $transactionId
	 *
	 * @return null
	 */
	public function getTransaction($transactionId) {
		$response = $this->call('transaction/' . $transactionId, null);

		if ($response) {
			return $response['data'];
		}

		return null;
	}

	public function getIdealIssuers() {
		$response = $this->call('settings', null);

		if ($response) {
			return $response['payment_methods']['ideal']['metadata']['issuers'];
		}

		return [];
	}

	public function call($action, $postData) {
		if ($this->accessToken) {
			$curl = curl_init($this->url . $action);
			$length = 0;

			if (is_array($postData)) {
				$jsonData = json_encode($postData);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);

				$length = strlen($jsonData);
			}

			$header = [
				'Accept: application/json',
				'Content-Type: application/json',
				'Content-Length: ' . $length,
				'Authorization: Bearer ' . $this->accessToken
			];

			curl_setopt($curl, CURLINFO_HEADER_OUT, true);
			curl_setopt($curl, CURLOPT_PORT, 443);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);

			$responseString = curl_exec($curl);
			$httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			curl_close($curl);

			if ($httpStatusCode >= 200 && $httpStatusCode < 300 && $responseString) {
				return json_decode($responseString, true);
			}
		}

		return null;
	}
}