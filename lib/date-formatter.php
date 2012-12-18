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

use ICanBoogie\DateTime;

/**
 * Provides date and time localization.
 *
 * The class allows you to format dates and times in a locale-sensitive manner using
 * {@link http://www.unicode.org/reports/tr35/#Date_Format_Patterns Unicode format patterns}.
 *
 * Original code: http://code.google.com/p/yii/source/browse/tags/1.1.7/framework/i18n/CDateFormatter.php
 */
class DateFormatter
{
	/**
	 * Pattern characters mapping to the corresponding translator methods.
	 *
	 * @var array
	 */
	static protected $formatters = array
	(
		'G' => 'format_era',
		'y' => 'format_year',
//		'Y' => Year (in "Week of Year" based calendars).
//		'u' => Extended year.
		'Q' => 'format_quarter',
		'q' => 'format_standalone_quarter',
		'M' => 'format_month',
		'L' => 'format_standalone_month',
//		'l' => Special symbol for Chinese leap month, used in combination with M. Only used with the Chinese calendar.
		'w' => 'format_week_of_year',
		'W' => 'format_week_of_month',
		'd' => 'format_day_of_month',
		'D' => 'format_day_of_year',
		'F' => 'format_day_of_week_in_month',

		'h' => 'format_hour12',
		'H' => 'format_hour24',
		'm' => 'format_minutes',
		's' => 'format_seconds',
		'E' => 'format_day_in_week',
		'c' => 'format_day_in_week',
		'e' => 'format_day_in_week',
		'a' => 'format_period',
		'k' => 'format_hour_in_day',
		'K' => 'format_hour_in_period',
		'z' => 'format_timezone',
		'Z' => 'format_timezone',
		'v' => 'format_timezone'
	);

	static public function get_date($time=null, $gmt=false)
	{
		if ($gmt)
		{
			$tz = date_default_timezone_get();
			date_default_timezone_set('GMT');
			$rc = getdate($time);
			date_default_timezone_set($tz);
		}
		else
		{
			$rc = getdate($time);
		}

		return $rc;
	}

	protected $locale;

	/**
	 * Constructor.
	 *
	 * @param Locale $locale
	 */
	public function __construct(Locale $locale)
	{
		$this->locale = $locale;
	}

	/**
	 * Parses the datetime format pattern.
	 *
	 * @param string $pattern the pattern to be parsed
	 *
	 * @return array tokenized parsing result
	 */
	protected function tokenize($pattern)
	{
		static $formats = array();

		if (isset($formats[$pattern]))
		{
			return $formats[$pattern];
		}

		$tokens = array();
		$is_literal = false;
		$literal = '';

		for ($i = 0, $n = strlen($pattern) ; $i < $n ; ++$i)
		{
			$c = $pattern{$i};

			if ($c === "'")
			{
				if ($i < $n-1 && $pattern{$i+1} === "'")
				{
					$tokens[] = "'";
					$i++;
				}
				else if ($is_literal)
				{
					$tokens[] = $literal;
					$literal = '';
					$is_literal = false;
				}
				else
				{
					$is_literal = true;
					$literal = '';
				}
			}
			else if ($is_literal)
			{
				$literal .= $c;
			}
			else
			{
				for ($j = $i + 1 ; $j < $n ; ++$j)
				{
					if ($pattern{$j} !== $c) break;
				}

				$l = $j-$i;
				$p = str_repeat($c, $l);

				$tokens[] = isset(self::$formatters[$c]) ? array(self::$formatters[$c], $p, $l) : $p;

				$i = $j - 1;
			}
		}

		if ($literal)
		{
			$tokens[] = $literal;
		}

		return $formats[$pattern] = $tokens;
	}

	/**
	 * Formats a date according to a pattern.
	 *
	 * @param string $pattern the pattern (See {@link http://www.unicode.org/reports/tr35/#Date_Format_Patterns})
	 * @param \DateTime|string|int $datetime Datetime to format.
	 *
	 * @return string formatted date time.
	 */
	public function format($datetime, $pattern)
	{
		if (is_numeric($datetime))
		{
			$datetime = new DateTime('@' . $datetime);
		}
		else if (is_string($datetime))
		{
			$datetime = new DateTime($datetime);
		}
		else if (!($datetime instanceof \DateTime))
		{
			throw new \InvalidArgumentException("Invalid type for <code>\$datetime</code>: " . gettype($datetime) . '.');
		}

		$tokens = $this->tokenize($pattern);

		$rc = '';

		foreach ($tokens as $token)
		{
			if (is_array($token)) // a callback: method name, sub-pattern
			{
				$token = $this->{$token[0]}($datetime, $token[1], $token[2]);
			}

			$rc .= $token;
		}

		return $rc;
	}

	/**
	 * Alias to the {@link format()} method.
	 */
	public function __invoke($time, $pattern)
	{
		return $this->format($time, $pattern);
	}

	/**
	 * Formats a date according to a predefined pattern.
	 *
	 * The predefined pattern is determined based on the date pattern width and time pattern width.
	 *
	 * @param mixed $timestamp UNIX timestamp or a string in strtotime format
	 * @param string $date_pattern width of the date pattern. It can be `full`, `long`, `medium`
	 * and `short`. If `null`, it means the date portion will NOT appear in the formatting result.
	 * @param string $time_pattern width of the time pattern. It can be `full`, `long`, `medium`
	 * and `short`. If null, it means the time portion will NOT appear in the formatting result.
	 *
	 * @return string formatted date time.
	 */
	public function format_datetime($timestamp, $date_pattern='medium', $time_pattern='medium')
	{
		$date = null;
		$time = null;

		$dates_conventions = $this->locale->conventions['dates'];
		$available_formats = $dates_conventions['dateTimeFormats'];

		if ($date_pattern)
		{
			$date_widths = $dates_conventions['dateFormats'];

			if (isset($date_widths[$date_pattern]))
			{
				$date_pattern = $date_widths[$date_pattern];
			}
			else if (isset($available_formats[$date_pattern]))
			{
				$date_pattern = $available_formats[$date_pattern];
			}

			$date = $this->format($timestamp, $date_pattern);
		}

		if ($time_pattern)
		{
			$time_widths = $dates_conventions['timeFormats'];

			if (isset($time_widths[$time_pattern]))
			{
				$time_pattern = $time_widths[$time_pattern];
			}
			else if (isset($available_formats[$time_pattern]))
			{
				$date_pattern = $available_formats[$time_pattern];
			}

			$time = $this->format($timestamp, $time_pattern);
		}

		if ($date && $time)
		{
			$date_time_pattern = isset($dates_conventions['date_time_format']) ? $dates_conventions['date_time_format'] : '{1} {0}';

			return strtr($date_time_pattern, array('{0}' => $time, '{1}' => $date));
		}

		return $date . $time;
	}

	/*
	 * era (G)
	 */

	/**
	 * Era - Replaced with the Era string for the current date. One to three letters for the
	 * abbreviated form, four letters for the long form, five for the narrow form. [1..3,4,5]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string era
	 * @todo How to support multiple Eras?, e.g. Japanese.
	 */
	protected function format_era(DateTime $datetime, $pattern, $length)
	{
		$era = ($datetime->year > 0) ? 1 : 0;

		switch($length)
		{
			case 1:
			case 2:
			case 3: return $this->locale->abbreviated_eras[$era];
			case 4: return $this->locale->wide_eras[$era];
			case 5: return $this->locale->narrow_eras[$era];
		}
	}

	/*
	 * year (y)
	 */

	/**
	 * Year. Normally the length specifies the padding, but for two letters it also specifies the
	 * maximum length. [1..n]
	 *
	 * @param Datetime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string formatted year
	 */
	protected function format_year(Datetime $datetime, $pattern, $length)
	{
		$year = $datetime->year;

		if ($length == 2)
		{
			$year = $year % 100;
		}

		return str_pad($year, $length, '0', STR_PAD_LEFT);
	}

	/*
	 * quarter (Q,q)
	 */

	/**
	 * Quarter - Use one or two "Q" for the numerical quarter, three for the abbreviation, or four
	 * for the full (wide) name. [1..2,3,4]
	 *
	 * @param \DateTime $datetime Datetime.
	 * @param string $pattern Pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string
	 */
	protected function format_quarter(DateTime $datetime, $pattern, $length)
	{
		$quarter = $datetime->quarter;

		switch ($length)
		{
			case 1: return $quarter;
			case 2: return str_pad($quarter, 2, '0', STR_PAD_LEFT);
			case 3: return $this->locale->abbreviated_quarters[$quarter];
			case 4: return $this->locale->wide_quarters[$quarter];
		}
	}

	/**
	 * Stand-Alone Quarter - Use one or two "q" for the numerical quarter, three for the
	 * abbreviation, or four for the full (wide) name. [1..2,3,4]
	 *
	 * @param array $date result of getdate().
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string
	 */
	protected function format_standalone_quarter(DateTime $datetime, $pattern, $length)
	{
		$quarter = $datetime->quarter;

		switch ($length)
		{
			case 1: return $quarter;
			case 2: return str_pad($quarter, 2, '0', STR_PAD_LEFT);
			case 3: return $this->locale->standalone_abbreviated_quarters[$quarter];
			case 4: return $this->locale->standalone_wide_quarters[$quarter];
		}
	}

	/*
	 * month (M|L)
	 */

	/**
	 * Month - Use one or two "M" for the numerical month, three for the abbreviation, four for
	 * the full name, or five for the narrow name. [1..2,3,4,5]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string
	 */
	protected function format_month(DateTime $datetime, $pattern, $length)
	{
		$month = $datetime->month;

		switch ($length)
		{
			case 1: return $month;
			case 2: return str_pad($month, 2, '0', STR_PAD_LEFT);
			case 3: return $this->locale->abbreviated_months[$month];
			case 4: return $this->locale->wide_months[$month];
			case 5: return $this->locale->narrow_months[$month];
		}
	}

	/**
	 * Stand-Alone Month - Use one or two "L" for the numerical month, three for the abbreviation,
	 * or four for the full (wide) name, or 5 for the narrow name. [1..2,3,4,5]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string formatted month.
	 */
	protected function format_standalone_month(DateTime $datetime, $pattern, $length)
	{
		$month = $datetime->month;

		switch ($length)
		{
			case 1: return $month;
			case 2: return str_pad($month, 2, '0', STR_PAD_LEFT);
			case 3: return $this->locale->standalone_abbreviated_months[$month];
			case 4: return $this->locale->standalone_wide_months[$month];
			case 5: return $this->locale->standalone_narrow_months[$month];
		}
	}

	/*
	 * week (w|W)
	 */

	/**
	 * Week of Year. [1..2]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return integer
	 */
	protected function format_week_of_year(DateTime $datetime, $pattern, $length)
	{
		if ($length > 2)
		{
			return;
		}

		$week = $datetime->week;

		return $length == 1 ? $week : str_pad($week, 2, '0', STR_PAD_LEFT);
	}

	/**
	 * Week of Month. [1]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return integer week of month
	 */
	protected function format_week_of_month(DateTime $datetime, $pattern, $length)
	{
		if ($length == 1)
		{
			return ceil($datetime->day / 7);
		}
	}

	/*
	 * day (d,D,F)
	 */

	/**
	 * Date - Day of the month. [1..2]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string day of the month
	 */
	protected function format_day_of_month(DateTime $datetime, $pattern, $length)
	{
		$day = $datetime->day;

		if ($length == 1)
		{
			return $day;
		}
		else if ($length == 2)
		{
			return str_pad($day, 2, '0', STR_PAD_LEFT);
		}
	}

	/**
	 * Day of year. [1..3]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string
	 */
	protected function format_day_of_year(DateTime $datetime, $pattern, $length)
	{
		$day = $datetime->year_day;

		if ($length > 3)
		{
			return;
		}

		return str_pad($day + 1, $length, '0', STR_PAD_LEFT);
	}

	/**
	 * Day of Week in Month. The example is for the 2nd Wed in July. [1]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return int
	 */
	protected function format_day_of_week_in_month(DateTime $datetime, $pattern, $length)
	{
		if ($length == 1)
		{
			return floor(($datetime->day + 6) / 7);
		}
	}

	/*
	 * weekday (E,e,c)
	 */

	/**
	 * Day of week - Use one through three letters for the short day, or four for the full name,
	 * five for the narrow name, or six for the short name. [1..3,4,5,6]
 	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string
	 */
	protected function format_day_in_week(DateTime $datetime, $pattern)
	{
		static $translate = array
		(
			1 => 'mon',
			2 => 'tue',
			3 => 'wed',
			4 => 'thu',
			5 => 'fri',
			6 => 'sat',
			7 => 'sun'
		);

		$day = $datetime->weekday;

		switch ($pattern)
		{
			case 'E':
			case 'EE':
			case 'EEE':
			case 'eee':
				return $this->locale->abbreviated_days[$translate[$day]];

			case 'EEEE':
			case 'eeee':
				return $this->locale->wide_days[$translate[$day]];

			case 'EEEEE':
			case 'eeeee':
				return $this->locale->narrow_days[$translate[$day]];

			case 'EEEEEE':
			case 'eeeeee':
				return $this->locale->short_days[$translate[$day]];

			case 'e':
			case 'ee':
			case 'c':
				return $day;

			case 'ccc':
				return $this->locale->standalone_abbreviated_days[$translate[$day]];

			case 'cccc':
				return $this->locale->standalone_wide_days[$translate[$day]];

			case 'ccccc':
				return $this->locale->standalone_narrow_days[$translate[$day]];

			case 'cccccc':
				return $this->locale->standalone_short_days[$translate[$day]];
		}
	}

	/*
	 * period (a)
	 */

	/**
	 * AM or PM. [1]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string AM or PM designator
	 */
	protected function format_period(DateTime $datetime, $pattern, $length)
	{
		return $this->locale->conventions['dates']['dayPeriods']['format']['abbreviated'][$datetime->hour < 12 ? 'am' : 'pm'];
	}

	/*
	 * hour (h,H,K,k)
	 */

	/**
	 * Hour [1-12]. When used in skeleton data or in a skeleton passed in an API for flexible data
	 * pattern generation, it should match the 12-hour-cycle format preferred by the locale
	 * (h or K); it should not match a 24-hour-cycle format (H or k). Use hh for zero
	 * padding. [1..2]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string
	 */
	protected function format_hour12(DateTime $datetime, $pattern, $length)
	{
		$hour = $datetime->hour;
		$hour = ($hour == 12) ? 12 : $hour % 12;

		if ($length == 1)
		{
			return $hour;
		}
		else if ($length == 2)
		{
			return str_pad($hour, 2, '0', STR_PAD_LEFT);
		}
	}

	/**
	 * Hour [0-23]. When used in skeleton data or in a skeleton passed in an API for flexible
	 * data pattern generation, it should match the 24-hour-cycle format preferred by the
	 * locale (H or k); it should not match a 12-hour-cycle format (h or K). Use HH for zero
	 * padding. [1..2]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string
	 */
	protected function format_hour24(DateTime $datetime, $pattern, $length)
	{
		$hour = $datetime->hour;

		if ($length == 1)
		{
			return $hour;
		}
		else if ($length == 2)
		{
			return str_pad($hour, 2, '0', STR_PAD_LEFT);
		}
	}

	/**
	 * Hour [0-11]. When used in a skeleton, only matches K or h, see above. Use KK for zero
	 * padding. [1..2]
	 *
	 * @param DateTime $datetime.
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return integer hours in AM/PM format.
	 */
	protected function format_hour_in_period(DateTime $datetime, $pattern, $length)
	{
		$hour = $datetime->hour % 12;

		if ($length == 1)
		{
			return $hour;
		}
		else if ($length == 2)
		{
			return str_pad($hour, 2, '0', STR_PAD_LEFT);
		}
	}

	/**
	 * Hour [1-24]. When used in a skeleton, only matches k or H, see above. Use kk for zero
	 * padding. [1..2]
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return integer
	 */
	protected function format_hour_in_day(DateTime $datetime, $pattern, $length)
	{
		$hour = $datetime->hour;

		if ($hour == 0)
		{
			$hour = 24;
		}

		if ($length == 1)
		{
			return $hour;
		}
		else if ($length == 2)
		{
			return str_pad($hour, 2, '0', STR_PAD_LEFT);
		}
	}

	/*
	 * minute (m)
	 */

	/**
	 * Minute. Use one or two "m" for zero padding.
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition
	 *
	 * @return string minutes.
	 */
	protected function format_minutes(DateTime $datetime, $pattern, $length)
	{
		$minutes = $datetime->minute;

		if ($length == 1)
		{
			return $minutes;
		}
		else if ($length == 2)
		{
			return str_pad($minutes, 2, '0', STR_PAD_LEFT);
		}
	}

	/*
	 * second
	 */

	/**
	 * Second. Use one or two "s" for zero padding.
	 *
	 * @param DateTime $datetime
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string seconds
	 */
	protected function format_seconds(DateTime $datetime, $pattern, $length)
	{
		$seconds = $datetime->second;

		if ($length == 1)
		{
			return $seconds;
		}
		else if ($length == 2)
		{
			return str_pad($seconds, 2, '0', STR_PAD_LEFT);
		}
	}

	/*
	 * zone (z,Z,v)
	 */

	/**
	 * Time Zone.
	 *
	 * @param DateTime $datetime.
	 * @param string $pattern a pattern.
	 * @param int $length Number of repetition.
	 *
	 * @return string time zone
	 */
	protected function format_timezone(DateTime $datetime, $pattern, $length)
	{
		if ($pattern{0} === 'z' || $pattern{0} === 'v')
		{
			return $datetime->format('T');
		}
		else if ($pattern{0} === 'Z')
		{
			return $datetime->format('O');
		}
	}
}