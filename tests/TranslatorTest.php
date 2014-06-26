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

class TranslatorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider provide_test_translate
	 */
	public function test_translate($native, $args, $options, $expected)
	{
		$t = Translator::from('en');

		$this->assertSame($expected, $t($native, $args, $options));
	}

	public function provide_test_translate()
	{
		return [

			[ "undefined", [], [ ], "undefined" ],
			[ "undefined", [], [ 'default' => "my default" ], "my default" ],
			[ "undefined", [], [ 'default' => null ], null ]

		];
	}
}