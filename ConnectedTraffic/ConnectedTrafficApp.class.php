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

// READ(!!): http://tools.ietf.org/html/rfc6455#section-5.5.3

namespace ConnectedTraffic;

use \ReflectionMethod as ReflectionMethod;
use \ConnectedTraffic as ConnectedTraffic;
use \ConnectedTraffic\Model\Response\Response as Response;

class ConnectedTrafficApp {
	
	private $allowedEvents = array(
		'onConnected',
		'onInitialized',
		'onReceived',
		'onSent',
		'onClosed',
	);
	private $config = array();
	private $responses = array();
	

	public function __construct(){
		$this->config = ConnectedTraffic::config()->getAppConfig();
		$this->_loadFiles();
	}

	private function _loadFiles(){
		if(!isset($this->config['includes']) || !is_array($this->config['includes']))
			return;
		foreach($this->config['includes'] as $include){
			require_once(APP_ROOT . '/' . $include);
		}
	}

	public function processEvent($eventType, $connectionId, $data = null){
		if(!in_array($eventType, $this->allowedEvents))
			return;
		foreach($this->config['eventControllers'] as $className){
			$controller = new $className(
				ConnectedTraffic::getCM()->getClients(),
				$connectionId
			);
			$arguments = array();
			if($data !== null){
				$arguments['data'] = $data;
			}
			call_user_func_array(array($controller, $eventType), $arguments);
		}
	}

	public function processRequest($request){
		echo "APP: processing request!\n";
		if (!$request->isValid()) {
			$response = new Response(
				$request->getSender(),
				null,
				-1,
				$request->getErrorMessage()
			);
			$this->responses[] = $response;
			return;
		}
		
		$parts = explode('/', $request->getHeader()->getAction());
		
		$className = ucfirst($parts[0]) . 'Controller';
		$methodName = 'action' . ucFirst($parts[1]);

		if (!in_array($className, $this->config['requestControllers']) ||
			!class_exists($className)) {
			$response = new Response(
				$request->getSender(),
				null,
				-4,
				'Request could be resolved!'
			);
			$this->responses[] = $response;
			return;
		}

		$controller = new $className(
			ConnectedTraffic::getCM()->getClients(),
			$request->getSender(),
			$request->getHeader(),
			$request->getBody()
		);

		if (!is_subclass_of($controller, 'RequestController')) {
			$response = new Response(
				$request->getSender(),
				null,
				-7,
				'Request could be resolved!'
			);
			$this->responses[] = $response;
			return;
		}

		if (!method_exists($controller, $methodName)) {
			$response = new Response(
				$request->getSender(),
				null,
				-5,
				'Request could be resolved!'
			);
			$this->responses[] = $response;
			return;
		}
		
		$arguments = array();
		$rm = new ReflectionMethod($className, $methodName);
		
		$params = $rm->getParameters();
		foreach ($params as $i => $param) {
			if (!$param->isOptional() && $request->getHeader()->getArgument($param->getName()) === null) {
				$response = new Response(
					$request->getSender(),
					null,
					-6,
					'Invalid arguments provided!'
				);
				$this->responses[] = $response;
				return false;
			}
			$arguments[] = $request->getHeader()->getArgument($param->getName());
		}


		call_user_func_array(array($controller, $methodName), $arguments);
		$this->responses = array_merge($this->responses, $controller->getResponses());
	}

	public function getResponse(){
		$response = null;
		if(count($this->responses) >0){
			$response = array_splice($this->responses, 0, 1);
			$response = $response[0];
		}
		return $response;
	}



}
