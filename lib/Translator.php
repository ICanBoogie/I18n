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

use ICanBoogie\AppConfig;
use ICanBoogie\FileCache;
use ICanBoogie\I18n;
use ICanBoogie\Prototyped;
use ICanBoogie\OffsetNotWritable;

class Translator implements \ArrayAccess
{
	use \ICanBoogie\PrototypeTrait;

	static private $translators = [];

	/**
	 * Return the translator for the specified locale.
	 *
	 * @param string $id The locale identifier.
	 *
	 * @return Translator The translator for the locale.
	 */
	static public function from($id)
	{
		if (isset(self::$translators[$id]))
		{
			return self::$translators[$id];
		}

		self::$translators[$id] = $translator = new static($id);

		return $translator;
	}

	static protected $cache;

	static protected function get_cache()
	{
		if (!self::$cache)
		{
			self::$cache = new FileCache([

				FileCache::T_COMPRESS => true,
				FileCache::T_REPOSITORY => \ICanBoogie\app()->config[AppConfig::REPOSITORY_CACHE] . '/core',
				FileCache::T_SERIALIZE => true

			]);
		}

		return self::$cache;
	}

	static public function messages_construct($id)
	{
		$messages = [];

		foreach (I18n::$load_paths as $path)
		{
			$filename = $path . DIRECTORY_SEPARATOR . $id . '.php';

			if (!file_exists($filename))
			{
				continue;
			}

			$messages[] = \ICanBoogie\array_flatten(require $filename);
		}

		return count($messages) ? call_user_func_array('array_merge', $messages) : [];
	}

	/**
	 * Translation messages.
	 *
	 * @var array
	 */
	protected $messages;

	protected function lazy_get_messages()
	{
		$messages = null;
		$id = $this->id;

		try
		{
			if (\ICanBoogie\app()->config['cache catalogs'])
			{
				$messages = self::get_cache()->load('i18n_' . $id, [ __CLASS__, 'messages_construct' ], $id);
			}
		}
		catch (\Exception $e)
		{
			#
			#
			#
		}

		if ($messages === null)
		{
			$messages = self::messages_construct($id);
		}

		if ($this->fallback)
		{
			$messages += $this->fallback->messages;
		}

		return $messages;
	}

	/**
	 * Fallback translator.
	 *
	 * @var Translator
	 */
	protected $fallback;

	/**
	 * Returns a translator fallback for this translator.
	 *
	 * @return Translator|null The translator fallback for this translator or null if there is
	 * none.
	 */
	protected function lazy_get_fallback()
	{
		list($id, $territory) = explode('-', $this->id) + [ 1 => null ];

		if (!$territory && $id == 'en')
		{
			return;
		}
		else if (!$territory)
		{
			$id = 'en';
		}

		return self::from($id);
	}

	/**
	 * Locale id for this translator.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Constructor.
	 *
	 * @param string $id Locale identifier
	 */
	protected function __construct($id)
	{
		unset($this->messages);
		unset($this->fallback);

		$this->id = $id;
	}

	static public $missing = [];

	/**
	 * Translate a native string in a locale string.
	 *
	 * @param string $native The native string to translate.
	 * @param array $args
	 * @param array $options
	 *
	 * @return string The translated string, or the same native string if no translation could be
	 * found.
	 */
	public function __invoke($native, array $args=[], array $options=[])
	{
		$native = (string) $native;
		$translated = null;

		$suffix = null;

		if ($args && array_key_exists(':count', $args))
		{
			$count = $args[':count'];

			if ($count == 0)
			{
				$suffix = '.none';
			}
			else if ($count == 1)
			{
				$suffix = '.one';
			}
			else
			{
				$suffix = '.other';
			}
		}

		$scope = I18n::get_scope();

		if (isset($options['scope']))
		{
			if ($scope)
			{
				$scope .= '.';
			}

			$scope .= is_array($options['scope']) ? implode('.', $options['scope']) : $options['scope'];
		}

		$prefix = $scope;
		$messages = $this->messages;

		while ($scope)
		{
			$try = $scope . '.' . $native . $suffix;

			if (isset($messages[$try]))
			{
				$translated = $messages[$try];

				break;
			}

			$pos = strpos($scope, '.');

			if ($pos === false)
			{
				break;
			}

			$scope = substr($scope, $pos + 1);
		}

		if ($translated)
		{
			$this->messages[($prefix ? $prefix . '.' : '') . $native] = $translated;
		}
		else
		{
			if (isset($messages[$native . $suffix]))
			{
				$translated = $messages[$native . $suffix];
			}
			else
			{
				self::$missing[] = ($prefix ? $prefix . '.' : '') . $native;
			}
		}

		if (!$translated)
		{
			$translated = $native;

			if (array_key_exists('default', $options))
			{
				$default = $options['default'];

				if (!($default instanceof \Closure))
				{
					return $default;
				}

				$translated = $default($this, $native, $options, $args) ?: $native;
			}
		}

		if ($args)
		{
			$translated = \ICanBoogie\format($translated, $args);
		}

		return $translated;
	}

	public function offsetExists($offset)
	{
		return isset($this->messages[$offset]);
	}

	public function offsetGet($offset)
	{
		return isset($this->messages[$offset]) ? $this->messages[$offset] : null;
	}

	public function offsetSet($offset, $value)
	{
		throw new OffsetNotWritable([ $offset, $this ]);
	}

	public function offsetUnset($offset)
	{
		throw new OffsetNotWritable([ $offset, $this ]);
	}
}
