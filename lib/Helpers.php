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
 * Patchable helpers of the ICanBoogie/I18n package.
 */
class Helpers
{
	static private $jumptable = [

		'get_locale'   => [ __CLASS__, 'get_locale' ],
		'set_locale'   => [ __CLASS__, 'set_locale' ],
		'get_language' => [ __CLASS__, 'get_language' ],
		'get_cldr'     => [ __CLASS__, 'get_cldr' ],
		't'            => [ __CLASS__, 't' ]

	];

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
	 * @param callable $callback Callback.
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
	 * @var \ICanBoogie\CLDR\Locale
	 */
	static private $locale;

	static private function get_locale($id=null)
	{
		return $id ? get_cldr()->locales[$id] : (self::$locale ? self::$locale : self::$locale = get_cldr()->locales['en']);
	}

	static private function set_locale($id)
	{
		return self::$locale = get_cldr()->locales[$id];
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
			$provider = new \ICanBoogie\CLDR\RunTimeProvider(new \ICanBoogie\CLDR\FileProvider(new \ICanBoogie\CLDR\WebProvider, REPOSITORY));
			$cldr = new \ICanBoogie\CLDR\Repository($provider);
		}

		return $cldr;
	}

	static private function t($str, array $args=[], array $options=[])
	{
		$locale_code = empty($options['language']) ? get_locale()->code : $options['language'];
		$translator = Translator::from($locale_code);

		return $translator($str, $args, $options);
	}
}
