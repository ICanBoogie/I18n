<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\I18n;

/**
 * Returns a locale.
 *
 * @param string $id Idenfitier of the locale or `null` to retrieve the current locale.
 *
 * @return \ICanBoogie\I18n\Locale
 */
function get_locale($id=null)
{
	return Helpers::get_locale($id);
}

/**
 * Sets the current locale.
 *
 * @param string $id Locale identifier.
 *
 * @return \ICanBoogie\I18n\Locale
 */
function set_locale($id)
{
	return Helpers::set_locale($id);
}

/**
 * Returns the language of the current locale.
 *
 * @return string The language of the current locale or `null` if there is none.
 */
function get_language()
{
	return Helpers::get_language();
}

/**
 * Translates a string using the current locale.
 *
 * @param string $str The native string to translate.
 * @param array $args Arguments used to format the translated string.
 * @param array $options Options for the translation.
 *
 * @return string The translated string.
 */
function t($str, array $args=array(), array $options=array())
{
	return Helpers::t($str, $args, $options);
}

/**
 * Formats a size in "b", "Kb", "Mb", "Gb" or "Tb".
 *
 * @param int $size
 *
 * @return string
 */
function format_size($size)
{
	if ($size < 1024)
	{
		$str = ":size\xC2\xA0b";
	}
	else if ($size < 1024 * 1024)
	{
		$str = ":size\xC2\xA0Kb";
		$size = $size / 1024;
	}
	else if ($size < 1024 * 1024 * 1024)
	{
		$str = ":size\xC2\xA0Mb";
		$size = $size / (1024 * 1024);
	}
	else if ($size < 1024 * 1024 * 1024 * 1024)
	{
		$str = ":size\xC2\xA0Gb";
		$size = $size / (1024 * 1024 * 1024);
	}
	else
	{
		$str = ":size\xC2\xA0Tb";
		$size = $size / (1024 * 1024 * 1024 * 1024);
	}

	return t($str, array(':size' => round($size)));
}

function format_number($number)
{
	$decimal_point = get_locale()->conventions['numbers']['symbols']['decimal'];
	$thousands_sep = ' ';

	return number_format($number, ($number - floor($number) < .009) ? 0 : 2, $decimal_point, $thousands_sep);
}

function format_currency($value, $currency)
{
	return get_locale()->number_formatter->format_currency($value, $currency);
}

function format_date($time, $pattern='default')
{
	$locale = get_locale();

	if ($pattern == 'default')
	{
		$pattern = $locale->conventions['dates']['dateFormats']['default'];
	}

	if (isset($locale->conventions['dates']['dateFormats'][$pattern]))
	{
		$pattern = $locale->conventions['dates']['dateFormats'][$pattern];
	}

	return $locale->date_formatter->format($time, $pattern);
}

function format_time($time, $pattern='default')
{
	$locale = get_locale();

	if ($pattern == 'default')
	{
		$pattern = $locale->conventions['dates']['timeFormats']['default'];
	}

	if (isset($locale->conventions['dates']['timeFormats'][$pattern]))
	{
		$pattern = $locale->conventions['dates']['timeFormats'][$pattern];
	}

	return $locale->date_formatter->format($time, $pattern);
}

function format_datetime($time, $date_pattern='default', $time_pattern='default')
{
	if (is_string($time))
	{
		$time = strtotime($time);
	}

	$locale = get_locale();

	if (isset($locale->conventions['dates']['dateTimeFormats']['availableFormats'][$date_pattern]))
	{
		$date_pattern = $locale->conventions['dates']['dateTimeFormats']['availableFormats'][$date_pattern];
		$time_pattern = null;
	}

	return $locale->date_formatter->format_datetime($time, $date_pattern, $time_pattern);
}

function date_period($date)
{
	static $relative;

	if (is_numeric($date))
	{
		$date_secs = $date;
		$date = date('Y-m-d', $date);
	}
	else
	{
		$date_secs = strtotime($date);
	}

	$today_days = strtotime(date('Y-m-d')) / (60 * 60 * 24);
	$date_days = strtotime(date('Y-m-d', $date_secs)) / (60 * 60 * 24);

	$diff = round($date_days - $today_days);
	$locale_id = get_language();

	if (empty($relative[$locale_id]))
	{
		$relative[$locale_id] = get_locale()->conventions['dates']['fields']['day']['relative'];
	}

	if (isset($relative[$locale_id][$diff]))
	{
		return $relative[$locale_id][$diff];
	}
	else if ($diff > -6)
	{
		return ucfirst(format_date($date_secs, 'EEEE'));
	}

	return format_date($date);
}

/**
 * Patchable helpers of the ICanBoogie/I18n package.
 */
class Helpers
{
	static private $jumptable = array
	(
		'get_locale' => array(__CLASS__, 'get_locale'),
		'set_locale' => array(__CLASS__, 'set_locale'),
		'get_language' => array(__CLASS__, 'get_language'),
		't' => array(__CLASS__, 't')
	);

	/**
	 * Calls the callback of a patchable function.
	 *
	 * @param string $name Name of the function.
	 * @param array $arguments Arguments.
	 *
	 * @return mixed
	 */
	static public function __callstatic($name, array $arguments)
	{
		return call_user_func_array(self::$jumptable[$name], $arguments);
	}

	/**
	 * Patches a patchable function.
	 *
	 * @param string $name Name of the function.
	 * @param collable $callback Callback.
	 *
	 * @throws \RuntimeException is attempt to patch an undefined function.
	 */
	static public function patch($name, $callback)
	{
		if (empty(self::$jumptable[$name]))
		{
			throw new \RuntimeException("Undefined patchable: $name.");
		}

		self::$jumptable[$name] = $callback;
	}

	/*
	 * Default implementations
	 */

	/**
	 * Current locale.
	 *
	 * @var \ICanBoogie\I18n\Locale
	 */
	static private $locale;

	static private function get_locale($id=null)
	{
		return $id ? Locale::get($id) : (self::$locale ? self::$locale : self::$locale = Locale::get('en'));
	}

	static private function set_locale($id)
	{
		return self::$locale = Locale::get($id);
	}

	static private function get_language()
	{
		return self::$locale ? self::$locale->language : null;
	}

	static private function t($str, array $args=array(), array $options=array())
	{
		$locale = get_locale(empty($options['language']) ? null : $options['language']);

		return $locale->translator($str, $args, $options);
	}
}