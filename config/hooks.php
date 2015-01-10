<?php

namespace ICanBoogie\I18n;

$hooks = __NAMESPACE__ . '\Hooks::';

return [

	'prototypes' => [

		'ICanBoogie\Core::get_locale' => $hooks . 'get_locale',
		'ICanBoogie\Core::set_locale' => $hooks . 'set_locale'

	]
];
