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

use ICanBoogie\Core;
use ICanBoogie\I18n;

class Hooks
{
	/*
	 * Autoconfig
	 */

	/**
	 * Adds the "locale" directories found in the app paths to `locale-path`.
	 *
	 * @param array $autoconfig
	 */
	static public function filter_autoconfig(array &$autoconfig)
	{
		foreach ($autoconfig['app-paths'] as $directory)
		{
			if (file_exists($directory . 'locale'))
			{
				$autoconfig['locale-path'][] = $directory . 'locale';
			}
		}
	}

	/*
	 * Events
	 */

	/**
	 * Event hook for `ICanBoogie\Core::boot`.
	 *
	 * Sets `I18n::$load_paths` using application config value `locale-paths`.
	 *
	 * @param Core\BootEvent $event
	 * @param Core $app
	 */
	static public function on_core_boot(Core\BootEvent $event, Core $app)
	{
		I18n::$load_paths = $app->config['locale-path'];
	}

	/*
	 * Prototypes
	 */

	/**
	 * Translates and formats a string.
	 *
	 * @param Core $app
	 * @param string $native
	 * @param array $args
	 * @param array $options
	 *
	 * @return string
	 *
	 * @see t
	 */
	static public function translate(Core $app, $native, array $args = [], array $options = [])
	{
		return t($native, $args, $options);
	}
}
