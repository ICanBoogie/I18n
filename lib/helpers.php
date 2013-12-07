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
 * Returns the CLDR representation.
 *
 * @return \ICanBoogie\CLDR\Repository
 */
function get_cldr()
{
	return Helpers::get_cldr();
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
	$locale = get_locale();
	$decimal_point = $locale['numbers']['symbols-numberSystem-' . $locale['numbers']['defaultNumberingSystem']]['decimal'];
	$thousands_sep = ' ';

	return number_format($number, ($number - floor($number) < .009) ? 0 : 2, $decimal_point, $thousands_sep);
}

function format_currency($value, $currency)
{
	return get_locale()->number_formatter->format_currency($value, $currency);
}

/**
 * Formats a date.
 *
 * @param mixed $datetime
 * @param string $pattern_or_width
 *
 * @see \ICanBoogie\CLDR\DateFormatter
 */
function format_date($datetime, $pattern_or_width='medium')
{
	return get_locale()->calendar->date_formatter->format($datetime, $pattern_or_width);
}

/**
 * Formats a time.
 *
 * @param mixed $datetime
 * @param string $pattern_or_width
 *
 * @see \ICanBoogie\CLDR\TimeFormatter
 */
function format_time($datetime, $pattern_or_width='medium')
{
	return get_locale()->calendar->time_formatter->format($datetime, $pattern_or_width);
}

/**
 * Formats a date and time.
 *
 * @param mixed $datetime
 * @param string $pattern_or_width_or_skeleton
 *
 * @see \ICanBoogie\CLDR\DateTimeFormatter
 */
function format_datetime($datetime, $pattern_or_width_or_skeleton='medium')
{
	return get_locale()->calendar->datetime_formatter->format($datetime, $pattern_or_width_or_skeleton);
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
		$locale = get_locale();
		$relative[$locale_id] = $locale['dateFields']['day'];
	}

	if (isset($relative[$locale_id]["relative-type-{$diff}"]))
	{
		return $relative[$locale_id]["relative-type-{$diff}"];
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
		'get_cldr' => array(__CLASS__, 'get_cldr'),
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
		return $id ? Locale::from($id) : (self::$locale ? self::$locale : self::$locale = Locale::from('en'));
	}

	static private function set_locale($id)
	{
		return self::$locale = Locale::from($id);
	}

	static private function get_language()
	{
		return self::$locale ? self::$locale->language : null;
	}

	static private function get_cldr()
	{
		static $cldr;

		if (!$cldr)
		{
			$provider = new \ICanBoogie\CLDR\Provider
			(
				new \ICanBoogie\CLDR\RunTimeCache(new \ICanBoogie\CLDR\FileCache(REPOSITORY)),
				new \ICanBoogie\CLDR\Retriever
			);

			$cldr = new \ICanBoogie\CLDR\Repository($provider);
		}

		return $cldr;
	}

	static private function t($str, array $args=array(), array $options=array())
	{
		$locale = get_locale(empty($options['language']) ? null : $options['language']);

		return $locale->translator($str, $args, $options);
	}
}