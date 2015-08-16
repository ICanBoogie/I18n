<?php

namespace ICanBoogie\I18n;

$hooks = Hooks::class . '::';

return [

	'prototypes' => [

		'ICanBoogie\Core::translate' => $hooks . 'translate'

	]

];
