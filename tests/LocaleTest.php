<?php

namespace ICanBoogie\I18n;

class LocaleTest extends \PHPUnit_Framework_TestCase
{
	static private $locale;

	static public function setupBeforeClass()
	{
		self::$locale = Locale::from('fr');
	}

	public function test_instance()
	{
		$this->assertInstanceOf('ICanBoogie\I18n\Locale', self::$locale);
		$this->assertInstanceOf('ICanBoogie\CLDR\Repository', self::$locale->repository);
	}

	public function test_get_calendar()
	{
		$this->assertInstanceOf('ICanBoogie\CLDR\Calendar', self::$locale->calendar);
	}

	public function test_get_number_formatter()
	{
		$this->assertInstanceOf('ICanBoogie\I18n\NumberFormatter', self::$locale->number_formatter);
	}

	public function test_get_translator()
	{
		$this->assertInstanceOf('ICanBoogie\I18n\Translator', self::$locale->translator);
	}
}
