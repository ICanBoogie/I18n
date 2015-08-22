<?php

namespace ICanBoogie\I18n;

use ICanBoogie;

$hooks = Hooks::class . '::';

return [

	ICanBoogie\Core::class . '::translate' => $hooks . 'translate'

];
