<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\I18n\Tests;

use ICanBoogie\DateTime;
use ICanBoogie\I18n\Locale;
use ICanBoogie\I18n\DateFormatter;

class DateFormatterTest extends \PHPUnit_Framework_TestCase
{
	public function testFormatYear1()
	{
		$formatter = Locale::get('en')->date_formatter;
		$datetime = new DateTime('0001-01-01');

		$this->assertEquals('1', $formatter($datetime, 'y'));
		$this->assertEquals('01', $formatter($datetime, 'yy'));
		$this->assertEquals('001', $formatter($datetime, 'yyy'));
		$this->assertEquals('0001', $formatter($datetime, 'yyyy'));
		$this->assertEquals('00001', $formatter($datetime, 'yyyyy'));
	}

	public function testFormatYear12()
	{
		$formatter = Locale::get('en')->date_formatter;
		$datetime = new DateTime('0012-01-01');

		$this->assertEquals('12', $formatter($datetime, 'y'));
		$this->assertEquals('12', $formatter($datetime, 'yy'));
		$this->assertEquals('012', $formatter($datetime, 'yyy'));
		$this->assertEquals('0012', $formatter($datetime, 'yyyy'));
		$this->assertEquals('00012', $formatter($datetime, 'yyyyy'));
	}

	public function testFormatYear123()
	{
		$formatter = Locale::get('en')->date_formatter;
		$datetime = new DateTime('0123-01-01');

		$this->assertEquals('123', $formatter($datetime, 'y'));
		$this->assertEquals('23', $formatter($datetime, 'yy'));
		$this->assertEquals('123', $formatter($datetime, 'yyy'));
		$this->assertEquals('0123', $formatter($datetime, 'yyyy'));
		$this->assertEquals('00123', $formatter($datetime, 'yyyyy'));
	}

	public function testFormatYear1234()
	{
		$formatter = Locale::get('en')->date_formatter;
		$datetime = new DateTime('1234-01-01');

		$this->assertEquals('1234', $formatter($datetime, 'y'));
		$this->assertEquals('34', $formatter($datetime, 'yy'));
		$this->assertEquals('1234', $formatter($datetime, 'yyy'));
		$this->assertEquals('1234', $formatter($datetime, 'yyyy'));
		$this->assertEquals('01234', $formatter($datetime, 'yyyyy'));
	}

	/*FAILING
	public function testFormatYear12345()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('12345', $formatter('12345-01-01', 'y'));
		$this->assertEquals('45', $formatter('12345-01-01', 'yy'));
		$this->assertEquals('12345', $formatter('12345-01-01', 'yyy'));
		$this->assertEquals('12345', $formatter('12345-01-01', 'yyyy'));
		$this->assertEquals('12345', $formatter('12345-01-01', 'yyyyy'));
	}
	*/

	/*
	 * MONTH
	 */

	public function testFormatMonth()
	{
		$formatter = Locale::get('en')->date_formatter;
		$datetime = new DateTime('2012-02-13');

		$this->assertEquals('2', $formatter($datetime, 'M'));
		$this->assertEquals('02', $formatter($datetime, 'MM'));
		$this->assertEquals('Feb', $formatter($datetime, 'MMM'));
		$this->assertEquals('February', $formatter($datetime, 'MMMM'));
		$this->assertEquals('F', $formatter($datetime, 'MMMMM'));
	}

	public function testFormatMonthinFrench()
	{
		$formatter = Locale::get('fr')->date_formatter;
		$datetime = new DateTime('2012-02-13');

		$this->assertEquals('2', $formatter($datetime, 'M'));
		$this->assertEquals('02', $formatter($datetime, 'MM'));
		$this->assertEquals('févr.', $formatter($datetime, 'MMM'));
		$this->assertEquals('février', $formatter($datetime, 'MMMM'));
		$this->assertEquals('F', $formatter($datetime, 'MMMMM'));
	}

	public function testFormatStandaloneMonth()
	{
		$formatter = Locale::get('en')->date_formatter;
		$datetime = new DateTime('2012-02-13');

		$this->assertEquals('2', $formatter($datetime, 'L'));
		$this->assertEquals('02', $formatter($datetime, 'LL'));
// 		$this->assertEquals('Feb', $formatter($datetime, 'LLL'));
		$this->assertEquals('February', $formatter($datetime, 'LLLL'));
		$this->assertEquals('F', $formatter($datetime, 'LLLLL'));
	}

	public function testFormatStandaloneMonthinFrench()
	{
		$formatter = Locale::get('fr')->date_formatter;
		$datetime = new DateTime('2012-02-13');

		$this->assertEquals('2', $formatter($datetime, 'L'));
		$this->assertEquals('02', $formatter($datetime, 'LL'));
// 		$this->assertEquals('févr.', $formatter($datetime, 'LLL'));
		$this->assertEquals('février', $formatter($datetime, 'LLLL'));
		$this->assertEquals('F', $formatter($datetime, 'LLLLL'));
	}

	/*
	 * WEEK
	 */

	public function testFormatWeekOfYear()
	{
		$formatter = Locale::get('en')->date_formatter;

// 		$this->assertEquals('1', $formatter('2012-01-01', 'w'));
		$this->assertEquals('1', $formatter('2012-01-02', 'w'));
		$this->assertEquals('01', $formatter('2012-01-02', 'ww'));
		$this->assertEquals('52', $formatter('2012-12-30', 'w'));
		$this->assertEquals('52', $formatter('2012-12-30', 'ww'));
	}

	public function testFormatWeekOfMonth()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1', $formatter('2012-01-01', 'W'));
		$this->assertEquals('1', $formatter('2012-01-02', 'W'));
		$this->assertEquals('2', $formatter('2012-01-09', 'W'));
		$this->assertEquals('3', $formatter('2012-01-16', 'W'));
		$this->assertEquals('4', $formatter('2012-01-23', 'W'));
		$this->assertEquals('5', $formatter('2012-01-30', 'W'));
	}

	/*
	 * DAY
	 */

	public function testFormatDayOfTheMonth()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1', $formatter('2012-01-01', 'd'));
		$this->assertEquals('01', $formatter('2012-01-01', 'dd'));
		$this->assertEquals('13', $formatter('2012-01-13', 'd'));
		$this->assertEquals('13', $formatter('2012-01-13', 'dd'));
	}

	public function testFormatDayOfYear()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1', $formatter('2012-01-01', 'D'));
		$this->assertEquals('01', $formatter('2012-01-01', 'DD'));
		$this->assertEquals('001', $formatter('2012-01-01', 'DDD'));
		$this->assertEquals('13', $formatter('2012-01-13', 'DD'));
		$this->assertEquals('013', $formatter('2012-01-13', 'DDD'));
		$this->assertEquals('165', $formatter('2012-06-13', 'DD'));
		$this->assertEquals('165', $formatter('2012-06-13', 'DDD'));
	}

	/* ? what is this supposed to be
	public function testFormatDayOfWeekInMonth()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1', $formatter('2012-06-01', 'F'));
		$this->assertEquals('3', $formatter('2012-06-03', 'F'));
		$this->assertEquals('5', $formatter('2012-06-05', 'F'));
	}
	*/

	/*
	 * WEEKDAY
	 */

	public function testFormatWeekday()
	{
		$formatter = Locale::get('en')->date_formatter;
		$datetime = new DateTime('2012-06-01');

		$this->assertEquals('Fri', $formatter($datetime, 'E'));
		$this->assertEquals('Fri', $formatter($datetime, 'EE'));
		$this->assertEquals('Fri', $formatter($datetime, 'EEE'));
		$this->assertEquals('Friday', $formatter($datetime, 'EEEE'));
		$this->assertEquals('F', $formatter($datetime, 'EEEEE'));
		$this->assertEquals('Fr', $formatter($datetime, 'EEEEEE'));
	}

	public function testFormatWeekdayInFrench()
	{
		$formatter = Locale::get('fr')->date_formatter;
		$datetime = new DateTime('2012-06-01');

		$this->assertEquals('ven.', $formatter($datetime, 'E'));
		$this->assertEquals('ven.', $formatter($datetime, 'EE'));
		$this->assertEquals('ven.', $formatter($datetime, 'EEE'));
		$this->assertEquals('vendredi', $formatter($datetime, 'EEEE'));
		$this->assertEquals('V', $formatter($datetime, 'EEEEE'));
		$this->assertEquals('ve', $formatter($datetime, 'EEEEEE'));
	}

	/*
	 * TODO: LocalWeekday
	 */

	/* FAILING: "stand-alone short" in not correct
	public function testFormatStandaloneWeekday()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('5', $formatter('2012-06-01', 'c'));
		$this->assertEquals('', $formatter('2012-06-01', 'cc'));
		$this->assertEquals('Fri', $formatter('2012-06-01', 'ccc'));
		$this->assertEquals('Friday', $formatter('2012-06-01', 'cccc'));
		$this->assertEquals('F', $formatter('2012-06-01', 'ccccc'));
		$this->assertEquals('Fr', $formatter('2012-06-01', 'cccccc'));
	}
	*/

	public function testFormatStandaloneWeekdayInFrench()
	{
		$formatter = Locale::get('fr')->date_formatter;

		$this->assertEquals('5', $formatter('2012-06-01', 'c'));
		$this->assertEquals('', $formatter('2012-06-01', 'cc'));
		$this->assertEquals('ven.', $formatter('2012-06-01', 'ccc'));
		$this->assertEquals('vendredi', $formatter('2012-06-01', 'cccc'));
		$this->assertEquals('V', $formatter('2012-06-01', 'ccccc'));
		$this->assertEquals('ven.', $formatter('2012-06-01', 'cccccc'));
	}

	/*
	 * PERIOD
	 */

	public function testPeriod()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('AM', $formatter('2012-06-01 00:00:00', 'a'));
		$this->assertEquals('AM', $formatter('2012-06-01 06:00:00', 'a'));
		$this->assertEquals('PM', $formatter('2012-06-01 18:00:00', 'a'));
		$this->assertEquals('PM', $formatter('2012-06-01 12:00:00', 'a'));
	}

	/*
	 * HOUR
	 */

	public function test12Hour()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('0', $formatter('2012-06-01 00:00:00', 'h'));
		$this->assertEquals('6', $formatter('2012-06-01 06:00:00', 'h'));
		$this->assertEquals('6', $formatter('2012-06-01 18:00:00', 'h'));
		$this->assertEquals('12', $formatter('2012-06-01 12:00:00', 'h'));

		$this->assertEquals('00', $formatter('2012-06-01 00:00:00', 'hh'));
		$this->assertEquals('06', $formatter('2012-06-01 06:00:00', 'hh'));
		$this->assertEquals('06', $formatter('2012-06-01 18:00:00', 'hh'));
		$this->assertEquals('12', $formatter('2012-06-01 12:00:00', 'hh'));
	}

	public function test24Hour()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('0', $formatter('2012-06-01 00:00:00', 'H'));
		$this->assertEquals('6', $formatter('2012-06-01 06:00:00', 'H'));
		$this->assertEquals('18', $formatter('2012-06-01 18:00:00', 'H'));
		$this->assertEquals('12', $formatter('2012-06-01 12:00:00', 'H'));

		$this->assertEquals('00', $formatter('2012-06-01 00:00:00', 'HH'));
		$this->assertEquals('06', $formatter('2012-06-01 06:00:00', 'HH'));
		$this->assertEquals('18', $formatter('2012-06-01 18:00:00', 'HH'));
		$this->assertEquals('12', $formatter('2012-06-01 12:00:00', 'HH'));
	}

	/*
	 * MINUTE
	 */

	public function testMinute()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1', $formatter('2012-06-01 23:01:23', 'm'));
		$this->assertEquals('01', $formatter('2012-06-01 23:01:23', 'mm'));
		$this->assertEquals('12', $formatter('2012-06-01 23:12:34', 'm'));
		$this->assertEquals('12', $formatter('2012-06-01 23:12:34', 'mm'));
	}

	/*
	 * SECOND
	 */

	public function testSecond()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1', $formatter('2012-06-01 23:47:01', 's'));
		$this->assertEquals('01', $formatter('2012-06-01 23:47:01', 'ss'));
		$this->assertEquals('12', $formatter('2012-06-01 23:47:12', 's'));
		$this->assertEquals('12', $formatter('2012-06-01 23:47:12', 'ss'));
	}

	/*
	 * ZONE
	 */

	/*
	public function testZone()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('CEST', $formatter('2012-06-01 01:23:45+0200', 'z'));
	}
	*/

	/*
	 * Quarter
	 */

	public function testFormatQuarterQ()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1', $formatter('2012-01-13', 'Q'));
		$this->assertEquals('1', $formatter('2012-02-13', 'Q'));
		$this->assertEquals('1', $formatter('2012-03-13', 'Q'));
		$this->assertEquals('2', $formatter('2012-04-13', 'Q'));
		$this->assertEquals('2', $formatter('2012-05-13', 'Q'));
		$this->assertEquals('2', $formatter('2012-06-13', 'Q'));
		$this->assertEquals('3', $formatter('2012-07-13', 'Q'));
		$this->assertEquals('3', $formatter('2012-08-13', 'Q'));
		$this->assertEquals('3', $formatter('2012-09-13', 'Q'));
		$this->assertEquals('4', $formatter('2012-10-13', 'Q'));
		$this->assertEquals('4', $formatter('2012-11-13', 'Q'));
		$this->assertEquals('4', $formatter('2012-12-13', 'Q'));
	}

	public function testFormatQuarterQQ()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('01', $formatter('2012-01-13', 'QQ'));
		$this->assertEquals('02', $formatter('2012-04-13', 'QQ'));
		$this->assertEquals('03', $formatter('2012-07-13', 'QQ'));
		$this->assertEquals('04', $formatter('2012-10-13', 'QQ'));
	}

	public function testFormatQuarterQQQ()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('Q1', $formatter('2012-01-13', 'QQQ'));
		$this->assertEquals('Q2', $formatter('2012-04-13', 'QQQ'));
		$this->assertEquals('Q3', $formatter('2012-07-13', 'QQQ'));
		$this->assertEquals('Q4', $formatter('2012-10-13', 'QQQ'));
	}

	public function testFormatQuarterQQQinFrench()
	{
		$formatter = Locale::get('fr')->date_formatter;

		$this->assertEquals('T1', $formatter('2012-01-13', 'QQQ'));
		$this->assertEquals('T2', $formatter('2012-04-13', 'QQQ'));
		$this->assertEquals('T3', $formatter('2012-07-13', 'QQQ'));
		$this->assertEquals('T4', $formatter('2012-10-13', 'QQQ'));
	}

	public function testFormatQuarterQQQQ()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1st quarter', $formatter('2012-01-13', 'QQQQ'));
		$this->assertEquals('2nd quarter', $formatter('2012-04-13', 'QQQQ'));
		$this->assertEquals('3rd quarter', $formatter('2012-07-13', 'QQQQ'));
		$this->assertEquals('4th quarter', $formatter('2012-10-13', 'QQQQ'));
	}

	public function testFormatQuarterQQQQinFrench()
	{
		$formatter = Locale::get('fr')->date_formatter;

		$this->assertEquals('1er trimestre', $formatter('2012-01-13', 'QQQQ'));
		$this->assertEquals('2e trimestre', $formatter('2012-04-13', 'QQQQ'));
		$this->assertEquals('3e trimestre', $formatter('2012-07-13', 'QQQQ'));
		$this->assertEquals('4e trimestre', $formatter('2012-10-13', 'QQQQ'));
	}

	/*
	 * Standalone Quarter
	 */
	public function testFormatStandaloneQuarter1()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1', $formatter('2012-01-13', 'q'));
		$this->assertEquals('1', $formatter('2012-02-13', 'q'));
		$this->assertEquals('1', $formatter('2012-03-13', 'q'));
		$this->assertEquals('2', $formatter('2012-04-13', 'q'));
		$this->assertEquals('2', $formatter('2012-05-13', 'q'));
		$this->assertEquals('2', $formatter('2012-06-13', 'q'));
		$this->assertEquals('3', $formatter('2012-07-13', 'q'));
		$this->assertEquals('3', $formatter('2012-08-13', 'q'));
		$this->assertEquals('3', $formatter('2012-09-13', 'q'));
		$this->assertEquals('4', $formatter('2012-10-13', 'q'));
		$this->assertEquals('4', $formatter('2012-11-13', 'q'));
		$this->assertEquals('4', $formatter('2012-12-13', 'q'));
	}

	public function testFormatStandaloneQuarter2()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('01', $formatter('2012-01-13', 'qq'));
		$this->assertEquals('02', $formatter('2012-04-13', 'qq'));
		$this->assertEquals('03', $formatter('2012-07-13', 'qq'));
		$this->assertEquals('04', $formatter('2012-10-13', 'qq'));
	}

	public function testFormatStandaloneQuarter3()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1st quarter', $formatter('2012-01-13', 'qqq'));
		$this->assertEquals('2nd quarter', $formatter('2012-04-13', 'qqq'));
		$this->assertEquals('3rd quarter', $formatter('2012-07-13', 'qqq'));
		$this->assertEquals('4th quarter', $formatter('2012-10-13', 'qqq'));
	}

	public function testFormatStandaloneQuarter3inFrench()
	{
		$formatter = Locale::get('fr')->date_formatter;

		$this->assertEquals('T1', $formatter('2012-01-13', 'qqq'));
		$this->assertEquals('T2', $formatter('2012-04-13', 'qqq'));
		$this->assertEquals('T3', $formatter('2012-07-13', 'qqq'));
		$this->assertEquals('T4', $formatter('2012-10-13', 'qqq'));
	}

	public function testFormatStandaloneQuarter4()
	{
		$formatter = Locale::get('en')->date_formatter;

		$this->assertEquals('1st quarter', $formatter('2012-01-13', 'qqqq'));
		$this->assertEquals('2nd quarter', $formatter('2012-04-13', 'qqqq'));
		$this->assertEquals('3rd quarter', $formatter('2012-07-13', 'qqqq'));
		$this->assertEquals('4th quarter', $formatter('2012-10-13', 'qqqq'));
	}

	public function testFormatStandaloneQuarter4inFrench()
	{
		$formatter = Locale::get('fr')->date_formatter;

		$this->assertEquals('1er trimestre', $formatter('2012-01-13', 'qqqq'));
		$this->assertEquals('2e trimestre', $formatter('2012-04-13', 'qqqq'));
		$this->assertEquals('3e trimestre', $formatter('2012-07-13', 'qqqq'));
		$this->assertEquals('4e trimestre', $formatter('2012-10-13', 'qqqq'));
	}
}