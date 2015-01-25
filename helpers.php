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

use ICanBoogie\CLDR\Currency;

/**
 * Returns a locale.
 *
 * @param string $id Identifier of the locale or `null` to retrieve the current locale.
 *
 * @return \ICanBoogie\CLDR\Locale
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
 * @return \ICanBoogie\CLDR\Locale
 * /
function set_locale($id)
{
	return Helpers::set_locale($id);
}
*/

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
function t($str, array $args=[], array $options=[])
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

	return t($str, [ ':size' => round($size) ]);
}

/**
 * Formats a number.
 *
 * @param $number
 * @param string|null $pattern
 *
 * @return string
 */
function format_number($number, $pattern=null)
{
	return get_locale()->format_number($number, $pattern);
}

/**
 * Formats a currency using the current locale.
 *
 * @param number $number
 * @param string|Currency $currency The currency code or a {@link Currency} instance.
 *
 * @return string
 */
function format_currency($number, $currency)
{
	if (!$currency instanceof Currency)
	{
		$currency = get_cldr()->currencies[$currency];
	}

	$localized_currency = get_locale()->localize($currency);

	return $localized_currency->format($number);
}

/**
 * Formats a date.
 *
 * @param mixed $datetime
 * @param string $pattern_or_width
 *
 * @return string
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
 * @return string
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
 * @return string
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
