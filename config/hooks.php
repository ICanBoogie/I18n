<?php

namespace ICanBoogie\I18n;

use ICanBoogie;

$hooks = Hooks::class . '::';

return [

	'events' => [

		ICanBoogie\Core::class . '::boot' => $hooks . 'on_core_boot'

	]

];
