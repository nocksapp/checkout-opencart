<?php


class NocksHelper
{
	const PLUGIN_VERSION = '0.0.1';
	const MODULE_CODE = 'payment_nocks';

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
}
