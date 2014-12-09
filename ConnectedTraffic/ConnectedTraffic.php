<?php

include(__DIR__ . '/include.php');

defined('SERVER_NAME') || define('SERVER_NAME', 'ConnectedTraffic');
defined('SERVER_VERSION') || define('SERVER_VERSION', '0.1');
defined('SERVER_IP') || define('SERVER_IP', getHostByName(getHostName()));

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
