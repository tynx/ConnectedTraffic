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

if (!defined('SERVER_NAME')) {
	define('SERVER_NAME', 'ConnectedTraffic');
}
if (!defined('SERVER_VERSION')) {
	define('SERVER_VERSION', '0.1');
}
if (!defined('SERVER_IP')) {
	define('SERVER_IP', getHostByName(getHostName()));
}

use \ConnectedTraffic\ConnectedTrafficServer as ConnectedTrafficServer;
use \ConnectedTraffic\ConnectedTrafficApp as ConnectedTrafficApp;
use \ConnectedTraffic\Model\ConnectionManager as ConnectionManager;
use \ConnectedTraffic\Component\Logging\Logger as Logger;
use \ConnectedTraffic\Component\Config as Config;

class ConnectedTraffic {
	private static $config = null;
	private static $connectionManager = null;
	private static $logger = null;
	private static $server = null;
	private static $app = null;

	public static function init($config){
		self::$connectionManager = new ConnectionManager();
		self::$config = new Config($config);
		self::$logger = new Logger();
		self::_createApp();
		self::_createServer();
	}

	private static function _createApp() {
		self::$app = new ConnectedTrafficApp();
	}

	private static function _createServer() {
		self::$server = new ConnectedTrafficServer();
	}

	public static function config(){
		return self::$config;
	}

	public static function app(){
		return self::$app;
	}

	public static function server(){
		return self::$server;
	}

	public static function getCM(){
		return self::$connectionManager;
	}

	public static function log(
		$message,
		$category = 'application',
		$level = 'info'
	) {
		self::$logger->log($message, $category, $level);
	}

/*	public static function getConfig($category) {
		return self::$config[$category];
	}

	public static function getParam($param) {
		if (isset(self::$config['params'][$param])) {
			return self::$config['params'][$param];
		}
		return null;
	}
*/

}
