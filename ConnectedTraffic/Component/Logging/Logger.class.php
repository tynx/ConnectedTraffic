<?php

namespace ConnectedTraffic\Component\Logging;

use \ConnectedTraffic\Exception\InvalidConfigException as InvalidConfigException;

// for logging
class Logger{

	private $logRoutes = array();
	private $config = array();

	public function __construct($config){
		$this->config = $config;
		new FileLogRoute();
		$this->addRoutes();
	}

	private function addRoutes(){
		foreach($this->config as $routeConfig){
			if(!isset($routeConfig['className']) || $routeConfig['className'] == '')
				throw new InvalidConfigException('No valid className for Logroute.');
			$className = null;
			if(class_exists($routeConfig['className'],false))
				$className = $routeConfig['className'];
			if(class_exists('\\ConnectedTraffic\\Component\\Logging\\' . $routeConfig['className'],false))
				$className = '\\ConnectedTraffic\\Component\\Logging\\' . $routeConfig['className'];
			if($className === null)
				throw new InvalidConfigException('No valid className for Logroute.');
			$route = new $className();
			$route->setConfig($routeConfig);
			$this->logRoutes[] = $route;
		}
	}

	public function log($message, $category, $level){
		foreach($this->logRoutes as $route)
			$route->log($message, $category, $level);
	}
}

?>
