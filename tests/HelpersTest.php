<?php

namespace ICanBoogie\I18n;

class HelpersTest extends \PHPUnit_Framework_TestCase
{
	static public function setupBeforeClass()
	{
		set_locale('fr');
	}

	public function test_get_locale()
	{
		$this->assertEquals('fr', get_locale());
	}

	// FIXME: that's not very conclusive because the locale doesn't feature a territory
	public function test_get_language()
	{
		$this->assertEquals('fr', get_language());
	}

	public function test_get_cldr()
	{
		$this->assertInstanceOf('ICanBoogie\CLDR\Repository', get_cldr());
	}

	/**
	 * @dataProvider provide_test_format_date
	 */
	public function test_format_date($locale, $datetime, $pattern_or_width, $expected)
	{
		set_locale($locale);

		$this->assertEquals($expected, format_date($datetime, $pattern_or_width));
	}

	public function provide_test_format_date()
	{
		return [

			[ 'en', '2013-11-05 21:22:23', 'full', 'Tuesday, November 5, 2013' ],
			[ 'en', '2013-11-05 21:22:23', 'long', 'November 5, 2013' ],
			[ 'en', '2013-11-05 21:22:23', 'medium', 'Nov 5, 2013' ],
			[ 'en', '2013-11-05 21:22:23', 'short', '11/5/13' ],

			[ 'fr', '2013-11-05 21:22:23', 'full', 'mardi 5 novembre 2013' ],
			[ 'fr', '2013-11-05 21:22:23', 'long', '5 novembre 2013' ],
			[ 'fr', '2013-11-05 21:22:23', 'medium', '5 nov. 2013' ],
			[ 'fr', '2013-11-05 21:22:23', 'short', '05/11/2013' ],

			# datetime patterns must be supported too
			[ 'en', '2013-11-05 21:22:23', ':GyMMMEd', 'Tue, Nov 5, 2013 AD' ],
			[ 'fr', '2013-11-05 21:22:23', 'd MMMM y', '5 novembre 2013' ]

		];
	}

	/**
	 * @dataProvider provide_test_format_time
	 */
	public function test_format_time($locale, $datetime, $pattern_or_width, $expected)
	{
		set_locale($locale);

		$this->assertEquals($expected, format_time($datetime, $pattern_or_width));
	}

	public function provide_test_format_time()
	{
		return [

			[ 'en', '2013-11-05 21:22:23', 'full', '9:22:23 PM CET' ],
			[ 'en', '2013-11-05 21:22:23', 'long', '9:22:23 PM CET' ],
			[ 'en', '2013-11-05 21:22:23', 'medium', '9:22:23 PM' ],
			[ 'en', '2013-11-05 21:22:23', 'short', '9:22 PM' ],

			[ 'fr', '2013-11-05 21:22:23', 'full', '21:22:23 CET' ],
			[ 'fr', '2013-11-05 21:22:23', 'long', '21:22:23 CET' ],
			[ 'fr', '2013-11-05 21:22:23', 'medium', '21:22:23' ],
			[ 'fr', '2013-11-05 21:22:23', 'short', '21:22' ],

			# datetime patterns must be supported too
			[ 'en', '2013-11-05 21:22:23', ':GyMMMEd', 'Tue, Nov 5, 2013 AD' ],
			[ 'fr', '2013-11-05 21:22:23', 'd MMMM y', '5 novembre 2013' ]

		];
	}

	/**
	 * @dataProvider provide_test_format_datetime
	 */
	public function test_format_datetime($locale, $datetime, $pattern_or_width_or_skeleton, $expected)
	{
		set_locale($locale);

		$this->assertEquals($expected, format_datetime($datetime, $pattern_or_width_or_skeleton));
	}

	public function provide_test_format_datetime()
	{
		return [

			[ 'en', '0005-01-01', 'y', '5' ],
			[ 'en', '2012-02-13', 'MMM', 'Feb' ],
			[ 'fr', '2012-02-13', 'MMMM', 'février' ],
			[ 'en', '2012-02-13', 'LLLL', 'February' ],
			[ 'fr', '2012-02-13', 'LLL', 'Févr.' ],

			# test: format width(full|long|medium|short)

			[ 'en', '2013-11-02 22:23:45', 'full', 'Saturday, November 2, 2013 at 10:23:45 PM CET' ],
			[ 'en', '2013-11-02 22:23:45', 'long', 'November 2, 2013 at 10:23:45 PM CET' ],
			[ 'en', '2013-11-02 22:23:45', 'medium', 'Nov 2, 2013, 10:23:45 PM' ],
			[ 'en', '2013-11-02 22:23:45', 'short', '11/2/13, 10:23 PM' ],

			# test: format width(full|long|medium|short) in french

			[ 'fr', '2013-11-02 22:23:45', 'full', 'samedi 2 novembre 2013 22:23:45 CET' ],
			[ 'fr', '2013-11-02 22:23:45', 'long', '2 novembre 2013 22:23:45 CET' ],
			[ 'fr', '2013-11-02 22:23:45', 'medium', '2 nov. 2013 22:23:45' ],
			[ 'fr', '2013-11-02 22:23:45', 'short', '02/11/2013 22:23' ]

		];
	}
}
