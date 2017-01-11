<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\I18n\Translator;

use ICanBoogie\Prototyped;

/**
 * The Proxi class creates translators, which can be used to easily translate string using
 * a same set of options.
 */
class Proxi extends Prototyped
{
	protected $options = [];

	public function __construct(array $options=[])
	{
		$this->options = $options;

		if (isset($this->options['scope']))
		{
			$this->scope = $this->options['scope'];
		}
	}

	protected function set_scope($scope)
	{
		if (is_array($scope))
		{
			$scope = implode('.', $scope);
		}

		$this->options['scope'] = $scope;
	}

	protected function set_language($language)
	{
		$this->options['language'] = $language;
	}

	protected function set_default($default)
	{
		$this->options['default'] = $default;
	}

	public function __invoke($str, array $args=[], array $options=[])
	{
		$options += $this->options;

		if (isset($options['scope']) && isset($this->options['scope']))
		{
			$scope = $options['scope'];

			if (is_array($scope))
			{
				$scope = implode('.', $scope);
			}

			if ($scope{0} == '.')
			{
				$options['scope'] = $this->options['scope'] . $scope;
			}
		}

		return \ICanBoogie\I18n\t($str, $args, $options);
	}
}
