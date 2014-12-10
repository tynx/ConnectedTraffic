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

include(__DIR__ . '/include.php');

if (defined('SERVER_NAME')) {
	define('SERVER_NAME', 'ConnectedTraffic');
}
if (defined('SERVER_VERSION')) {
	define('SERVER_VERSION', '0.1');
}
if (!defined('SERVER_IP')) {
	define('SERVER_IP', getHostByName(getHostName()));
}

use \ConnectedTraffic\ConnectedTrafficServer as ConnectedTrafficServer;
use \ConnectedTraffic\Component\Logging\Logger as Logger;

class ConnectedTraffic {
	private static $config = null;
	private static $logger = null;
	private static $server = null;

	public static function createServer($config) {
		self::$config = $config;
		if (isset($config['components']['logging'])) {
			self::$logger = new Logger($config['components']['logging']);
		}
		self::$server = new ConnectedTrafficServer();
		return self::$server;
	}

	public static function log(
		$message,
		$category = 'application',
		$level = 'info'
	) {
		self::$logger->log($message, $category, $level);
	}

	public static function getConfig($category) {
		return self::$config[$category];
	}

	public static function getParam($param) {
		if (isset(self::$config['params'][$param])) {
			return self::$config['params'][$param];
		}
		return null;
	}
}
