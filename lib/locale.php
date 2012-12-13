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

use ICanBoogie\Prototype\MethodNotDefined;

/**
 * A locale refers to a set of user preferences that tend to be shared across significant swaths
 * of the world. Traditionally, the data associated with it provides support for formatting and
 * parsing of dates, times, numbers, and currencies; for measurement units, for
 * sort-order (collation), plus translated names for time zones, languages, countries, and
 * scripts. The data can also include support for text boundaries (character, word, line,
 * and sentence), text transformations (including transliterations), and other services.
 *
 * @property-read array $conventions The UNICODE conventions for the locale.
 * @property-read DateFormatter $date_formatter The data formatter for the locale.
 * @property-read string $id Locale id
 * @property-read string $language Language of the locale.
 * @property-read NumberFormatter $number_formatter The number formatter for the locale.
 * @property-read string $territory Territory of the locale.
 * @property-read Translator $translator The translator for the locale.
 *
 * @property-read standalone_abbreviated_days
 * @property-read standalone_abbreviated_eras
 * @property-read standalone_abbreviated_months
 * @property-read standalone_abbreviated_quarters
 * @property-read standalone_narrow_days
 * @property-read standalone_narrow_eras
 * @property-read standalone_narrow_months
 * @property-read standalone_narrow_quarters
 * @property-read standalone_wide_days
 * @property-read standalone_wide_eras
 * @property-read standalone_wide_months
 * @property-read standalone_wide_quarters
 * @property-read abbreviated_days
 * @property-read abbreviated_eras
 * @property-read abbreviated_months
 * @property-read abbreviated_quarters
 * @property-read narrow_days
 * @property-read narrow_eras
 * @property-read narrow_months
 * @property-read narrow_quarters
 * @property-read wide_days
 * @property-read wide_eras
 * @property-read wide_months
 * @property-read wide_quarters
 *
 * @property-read standalone_short_days
 * @property-read short_days
 */
class Locale extends \ICanBoogie\Object
{
	/**
	 * Instantiated locales.
	 *
	 * @var array[string]Locale
	 */
	static private $locales = array();

	/**
	 * Returns the locale for the specified id.
	 *
	 * @param string $id The locale id.
	 *
	 * @return Locale.
	 */
	static public function get($id)
	{
		if (isset(self::$locales[$id]))
		{
			return self::$locales[$id];
		}

		return self::$locales[$id] = new static($id);
	}

	/**
	 * Language identifier.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Language of the locale.
	 *
	 * @var string
	 */
	protected $language;

	/**
	 * Territory code for this locale.
	 *
	 * @var string
	 */
	protected $territory;

	/**
	 * Initializes the {@link $language} and {@link $territory} properties.
	 *
	 * @param string $id Locale identifier.
	 */
	protected function __construct($id)
	{
		$id = strtr($id, '-', '_');
		$this->id = $id;

		list($this->language, $this->territory) = explode('_', $id) + array(1 => null);
	}

	/**
	 * Overrides the method to support composed properties for days, eras and months.
	 *
	 * The following pattern is supported for composed properties:
	 *
	 *     ^(standalone_)?(abbreviated|narrow|wide)_(days|eras|months|quarters)$
	 *
	 * For example, one can get the following properties:
	 *
	 *     $locale->abbreviated_months
	 *     $locale->standalone_abbreviated_months
	 *     $locale->wide_days
	 *     $locale->narrow_eras
	 *
	 * Fallbacks are available for the `narrows_eras` and `standalone_.+` properties:
	 *
	 * - If there is no definition available for narrow eras in the locale, the abbreviated
	 * convention is used instead.
	 * - If there is no stand-alone definition available, the "format" convention is used instead.
	 *
	 * @see http://unicode.org/reports/tr35/tr35-6.html#Calendar_Elements
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function __get($property)
	{
		static $readers = array('id', 'language', 'territory');

		if (in_array($property, $readers))
		{
			return $this->$property;
		}

		if (preg_match('#^(standalone_)?(abbreviated|narrow|short|wide)_(days|eras|months|quarters)$#', $property, $matches))
		{
			list(, $standalone, $width, $type) = $matches;

			$dates = $this->conventions['dates'];

			if ($type == 'eras')
			{
				if ($width == 'narrow' && empty($dates[$type][$width]))
				{
					$width = 'abbreviated';
				}

				$value = $dates[$type][$width];
			}
			else
			{
				$context = $standalone ? 'stand-alone' : 'format';

				if ($standalone && empty($dates[$type][$context][$width]))
				{
					$context = 'format';
				}

				if ($width == 'narrow' && empty($dates[$type][$context][$width]))
				{
					$width = 'abbreviated';
				}

				if ($width == 'abbreviated' && empty($dates[$type][$context][$width]))
				{
					$width = 'wide';
				}

				$value = $dates[$type][$context][$width];
			}

			return $this->$property = $value;
		}

		return parent::__get($property);
	}

	public function __call($method, $arguments)
	{
		if (is_callable(array($this, $method)))
		{
			return call_user_func_array($this->$method, $arguments);
		}

		throw new MethodNotDefined(array($method, $this));
	}

	/**
	 * Returns the locale identifier.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->id;
	}

	/**
	 * Returns the conventions of the locale.
	 *
	 * Conventions are loaded from the {@link CONVENTIONS_DIRECTORY} directory.
	 *
	 * @return array
	 */
	protected function get_conventions()
	{
		$try = CONVENTIONS_DIRECTORY . $this->id . '.php';

		if (file_exists($try))
		{
			return require $try;
		}

		$try = CONVENTIONS_DIRECTORY . $this->language . '.php';

		if (file_exists($try))
		{
			return require $try;
		}

		return require CONVENTIONS_DIRECTORY . 'en.php';
	}

	/**
	 * Returns the number formatter for the locale.
	 *
	 * @return NumberFormatter
	 */
	protected function get_number_formatter()
	{
		return new NumberFormatter($this);
	}

	/**
	 * Returns the date formatter for the locale.
	 *
	 * @return DateFormatter
	 */
	protected function get_date_formatter()
	{
		return new DateFormatter($this);
	}

	/**
	 * Returns the string translator for the locale.
	 *
	 * @return Translator
	 */
	protected function get_translator()
	{
		return Translator::get($this->id);
	}
}