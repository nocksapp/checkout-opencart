<?php


class NocksHelper
{
	const PLUGIN_VERSION = '0.0.1';

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

	public static function getPath($to = 'nocks')
	{
		return NocksHelper::isOpenCart23x() ? 'extension/payment/' . $to : 'payment/' . $to;
	}

	/**
	 * @return string
	 */
	public static function getModuleCode()
	{
		if (self::isOpenCart3x()) {
			return 'payment_nocks';
		}

		return 'nocks';
	}

	public static function getTemplate($template)
	{
		$template = static::getPath($template);

		if (!NocksHelper::isOpenCart3x()) {
			$template .= '.tpl';
		}

		return $template;
	}
}
