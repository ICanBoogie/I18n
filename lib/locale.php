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

use ICanBoogie\CLDR\Repository;
use ICanBoogie\Prototype\MethodNotDefined;

/**
 * A locale refers to a set of user preferences that tend to be shared across significant swaths
 * of the world. Traditionally, the data associated with it provides support for formatting and
 * parsing of dates, times, numbers, and currencies; for measurement units, for
 * sort-order (collation), plus translated names for time zones, languages, countries, and
 * scripts. The data can also include support for text boundaries (character, word, line,
 * and sentence), text transformations (including transliterations), and other services.
 *
 * @property-read string $id Locale id
 * @property-read string $language Language of the locale.
 * @property-read string $territory Territory of the locale.
 * @property-read array $calendar The data of the default calendar for the locale.
 * @property-read Conventions $conventions The UNICODE conventions for the locale.
 * @property-read DateFormatter $date_formatter The data formatter for the locale.
 * @property-read NumberFormatter $number_formatter The number formatter for the locale.
 * @property-read Translator $translator The translator for the locale.
 */
class Locale extends \ICanBoogie\CLDR\Locale
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
	static public function from($id)
	{
		if (isset(self::$locales[$id]))
		{
			return self::$locales[$id];
		}

		return self::$locales[$id] = new static(get_cldr(), $id);
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
	 * @param string $id Locale identifier. The underscore character "_" is replace with the
	 * hypen-minus character "-" as advised by the {@link http://www.rfc-editor.org/rfc/bcp/bcp47.txt BCP 47}.
	 */
	public function __construct(Repository $repository, $id)
	{
		$id = strtr($id, '_', '-');
		$this->id = $id;

		list($this->language, $this->territory) = explode('-', $id) + array(1 => null);

		parent::__construct($repository, $id);
	}

	public function __get($property)
	{
		switch ($property)
		{
			case 'id': return $this->id;
			case 'language': return $this->language;
			case 'territory': return $this->territory;
			case 'calendar': return $this->$property = $this->get_calendar();
			case 'number_formatter': return $this->$property = $this->get_number_formatter();
			case 'translator': return $this->$property = $this->get_translator();
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
	 * Returns the data of the default calendar for the locale.
	 *
	 * @return array
	 */
	protected function get_calendar()
	{
		return $this->calendars['gregorian'];
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
	 * Returns the string translator for the locale.
	 *
	 * @return Translator
	 */
	protected function get_translator()
	{
		return Translator::get($this->id);
	}
}