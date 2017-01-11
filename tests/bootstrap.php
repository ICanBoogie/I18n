<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Madrid');

class Application extends Core
{
	use Binding\I18n\ApplicationBindings;
}

boot(array_merge_recursive(get_autoconfig(), [

	'config-path' => [

		__DIR__ . '/../config' => Autoconfig\Config::CONFIG_WEIGHT_APP,
		__DIR__ . '/config' => Autoconfig\Config::CONFIG_WEIGHT_APP

	],

]));
