<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define('ICanBoogie\I18n\REPOSITORY', __DIR__ . '/repository');

require __DIR__ . '/../vendor/autoload.php';

if (!file_exists(ICanBoogie\I18n\REPOSITORY))
{
	mkdir(ICanBoogie\I18n\REPOSITORY);
}

date_default_timezone_set('Europe/Madrid');