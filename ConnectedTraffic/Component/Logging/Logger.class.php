<?php
/*
 * This file is part of ConnectedTraffic.
 *
 * ConnectedTraffic is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or any later version.

 * ConnectedTraffic is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with ConnectedTraffic.  If not, see
 * <http://www.gnu.org/licenses/>.
 */

namespace ConnectedTraffic\Component\Logging;

use \ConnectedTraffic\Exception\InvalidConfigException
	as InvalidConfigException;

// for logging
class Logger {

	private $logRoutes = array();
	private $config = array();

	public function __construct($config) {
		$this->config = $config;
		$this->_addRoutes();
	}

	private function _addRoutes() {
		foreach ($this->config as $routeConfig) {
			if (!isset($routeConfig['className']) ||
				empty($routeConfig['className'])) {
				throw new InvalidConfigException(
					'No valid className for Logroute.'
				);
			}
			$found = false;
			$className = $routeConfig['className'];
			if (class_exists($className, false)) {
				$found = true;
			}
			
			$className = '\\ConnectedTraffic\\Component\\Logging\\' .
				$className;
			if (class_exists($className, false)) {
				$found = true;
			}
			if ($found === false) {
				throw new InvalidConfigException(
					'No valid className for Logroute.'
				);
			}
			$route = new $className();
			$route->setConfig($routeConfig);
			$this->logRoutes[] = $route;
		}
	}

	public function log($message, $category, $level) {
		foreach ($this->logRoutes as $route) {
			$route->log($message, $category, $level);
		}
	}
}
