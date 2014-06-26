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

use ICanBoogie\I18n\Locale;

class I18n
{
	/**
	 * Paths were messages catalogs can be found.
	 *
	 * @var array
	 */
	static public $load_paths = [];

	static private $scope;
	static private $scope_chain = [];

	static public function push_scope($scope)
	{
		array_push(self::$scope_chain, self::$scope);

		self::$scope = (array) $scope;
	}

	static public function pop_scope()
	{
		self::$scope = array_pop(self::$scope_chain);
	}

	static public function get_scope()
	{
		$scope = self::$scope;

		if (is_array($scope))
		{
			$scope = implode('.', $scope);
		}

		return $scope;
	}
}