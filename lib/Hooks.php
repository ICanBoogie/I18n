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

use ICanBoogie\I18n;

class Hooks
{
	/**
	 * Adds the "locale" directories found in the app paths to `locale-path`.
	 *
	 * @param array $autoconfig
	 */
	static public function filter_autoconfig(array &$autoconfig, $root)
	{
		$directories = \ICanBoogie\resolve_app_paths($root);

		foreach ($directories as $directory)
		{
			if (file_exists($directory . 'locale'))
			{
				$autoconfig['locale-path'][] = $directory . 'locale';
			}
		}
	}

	/*
	 * Prototypes
	 */

	/**
	 * Sets the working locale.
	 *
	 * @param string $id Locale identifier.
	 */
	static public function set_locale(\ICanBoogie\Core $app, $id)
	{
		I18n\set_locale($id);
	}

	/**
	 * Returns the working locale.
	 *
	 * @return I18n\Locale
	 */
	static public function get_locale()
	{
		return I18n\get_locale();
	}
}
