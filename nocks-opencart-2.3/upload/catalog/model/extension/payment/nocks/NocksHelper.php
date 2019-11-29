<?php

class NocksHelper
{
	const PLUGIN_VERSION = '1.4.0';

	/**
	 * @return bool
	 */
	public static function isOpenCart3x()
	{
		return version_compare(VERSION, '3.0.0', '>=');
	}

	/**
	 * @return bool
	 */
	public static function isOpenCart23x()
	{
		return version_compare(VERSION, '2.3.0', '>=');
	}

	/**
	 * @return bool
	 */
	public static function isOpenCart2x()
	{
		return version_compare(VERSION, '2', '>=');
	}

	public static function getPath($methodID = null)
	{
		$suffix = $methodID ? '_' . $methodID : '';

		return NocksHelper::isOpenCart23x() ? 'extension/payment/nocks' . $suffix : 'payment/nocks' . $suffix;
	}

	/**
	 * @param $methodID
	 *
	 * @return string
	 */
	public static function getModuleCode($methodID = null)
	{
		$suffix = $methodID ? '_' . $methodID : '';

		if (self::isOpenCart3x()) {
			return 'payment_nocks' . $suffix;
		}

		return 'nocks' . $suffix;
	}

	public static function getTemplate($template)
	{
		$template = NocksHelper::isOpenCart23x() ? 'extension/payment/' . $template : 'payment/' . $template;

		if (!NocksHelper::isOpenCart3x()) {
			$template .= '.tpl';
		}

		return $template;
	}

	public static function getPaymentMethods()
	{
		return [
			[
				'id' => 'gulden',
			],
			[
				'id' => 'ideal',
			],
			[
				'id' => 'sepa',
			],
			[
				'id' => 'balance',
			],
			[
				'id' => 'bitcoin',
			],
			[
				'id' => 'litecoin',
			],
			[
				'id' => 'ethereum',
			]
		];
	}

	public static function withLabels($t, $methods) {
		$withLabels = [];
		foreach ($methods as $method) {
			$method['label'] = $t->get('payment_method_' . $method['id']);
			$withLabels[] = $method;
		}

		return $withLabels;
	}

	public static function prefixSettingsArray($array, $prefix) {
		return array_combine(
			array_map(function($k) use ($prefix) { return $prefix . $k; }, array_keys($array)),
			$array
		);
	}
}
