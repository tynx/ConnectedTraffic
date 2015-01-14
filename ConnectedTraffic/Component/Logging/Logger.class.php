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

use \ConnectedTraffic as ConnectedTraffic;
use \ConnectedTraffic\Exception\InvalidConfigException
	as InvalidConfigException;

/**
 * This class handles all the logging. If a log message is to be set,
 * it routes the message to all configured LogRoutes. This means the
 * same message can be logged on all Routes if needed/wished.
 */
class Logger {

	/**
	 * All the logRoute-objects are stored in here.
	 */
	private $logRoutes = array();


	public function __construct() {
		$config = ConnectedTraffic::config()->getComponentConfig('logging');
		if (!is_array($config)) {
			throw new InvalidConfigException(
				'No valid config provided for Logging!'
			);
		} else {
			foreach ($config as $routeConfig) {
				$this->_addRoute($routeConfig);
			}
		}
	}

	private function _addRoute($routeConfig) {
		if (!is_array($routeConfig)) {
			throw new InvalidConfigException(
				'No valid config provided for LogRoute!'
			);
		}
		if (!isset($routeConfig['className']) ||
			empty($routeConfig['className'])) {
			throw new InvalidConfigException(
				'No valid className for Logroute.'
			);
		}
		$className = '\\ConnectedTraffic\\Component\\Logging\\' .
			$routeConfig['className'];
		$route = null;
		
		if (class_exists($className, false)) {
			$route = new $className();
		}
		
		$className = $routeConfig['className'];
		if ($route === null && class_exists($className, false)) {
			$route = new $className();
		}
		if ($route === null) {
			throw new InvalidConfigException(
				'No valid className for Logroute.'
			);
		}
		$route->setConfig($routeConfig);
		$this->logRoutes[] = $route;
	}

	public function log( $message, $category, $level) {
		foreach ($this->logRoutes as $route) {
			$route->log($message, $category, $level);
		}
	}
}
