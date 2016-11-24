<?php

namespace ICanBoogie\I18n;

use ICanBoogie;

$hooks = Hooks::class . '::';

return [

	ICanBoogie\Application::class . '::boot' => $hooks . 'on_app_boot'

];
