<?php

namespace ConnectedTraffic\Controller;

use \ReflectionMethod as ReflectionMethod;
use \ConnectedTraffic as ConnectedTraffic;
use \ConnectedTraffic\Component\Config as Config;
use \ConnectedTraffic\Model\Request\Request as Request;
use \ConnectedTraffic\Model\Response\Response as Response;
use \Client as Client;

class RequestController{

	private $eventController = null;
	private $config = null;
	private $clients = array();
	private $requests = array();
	private $responses = array();

	public function __construct(){
		$this->config = ConnectedTraffic::getConfig('app');
		if(isset($this->config['eventController']) && $this->config['eventController']!=''){
			$className = $this->config['eventController'];
			if(file_exists($this->config['controllerDirectory'] . $className . '.class.php')){
				require_once($this->config['controllerDirectory'] . $className . '.class.php');
				if(class_exists($className)){
					$this->eventController = new $className();
				}
			}
		}
	}

	public function addClient($connectionId){
		$this->clients[] = new Client($connectionId);
	}

	public function removeClient($connectionId){
		foreach($this->clients as $i=>$client)
			if($client->getConnectionId() == $connectionId)
				array_splice($this->clients, $i, 1);
	}

	public function addRequest($sender, $payload){
		$this->requests[] = new Request($sender, $payload);
	}

	public function onConnectClient($connectionId){
		if($this->eventController !== null){
			$this->eventController->onConnect($this->clients, $connectionId);
			$this->handleEventResponses();
		}
	}

	public function onCloseClient($connectionId){
		if($this->eventController !== null){
			$this->eventController->onClose($this->clients, $connectionId);
			$this->handleEventResponses();
		}
	}

	public function run(){
		while(count($this->requests)>0){
			$request = array_splice($this->requests, 0, 1);
			$this->processRequest($request[0]);
		}
	}

	private function processRequest($request){
		if(!$request->isValid()){
			$response = new Response($request->getSender(), null, -1, $request->getErrorMessage());
			$this->registerResponse($response);
			return;
		}
		
		$parts = explode('/', $request->getHeader()->getAction());
		
		if(count($parts) != 2){
			$response = new Response($request->getSender(), null, -2, 'Invalid action provided!');
			$this->registerResponse($response);
			return;
		}
		
		$controller = $parts[0];
		$method = $parts[1];
		
		if($method == '' || $controller == ''){
			$response = new Response($request->getSender(), null, -3, 'Invalid action provided!');
			$this->registerResponse($response);
			return;
		}
		
		$className = ucfirst($controller) . 'Controller';
		$methodName = 'action' . ucFirst($method);
		if(!file_exists($this->config['controllerDirectory'] . $className . '.class.php')){
			$response = new Response($request->getSender(), null, -4, 'Request could be resolved!');
			$this->registerResponse($response);
			return false;
		}
		require_once($this->config['controllerDirectory'] . $className . '.class.php');
		if(!class_exists($className)){
			$response = new Response($request->getSender(), null, -5, 'Request could be resolved!');
			$this->registerResponse($response);
			return;
		}

		$controller = new $className($this->clients, $request->getSender(), $request->getHeader(), $request->getBody());

		if(!method_exists($controller, $methodName)){
			$response = new Response($request->getSender(), null, -6, 'Request could be resolved!');
			$this->registerResponse($response);
			return;
		}
		
		$arguments = array();
		$r = new ReflectionMethod($className, $methodName);
		
		$params = $r->getParameters();
		foreach ($params as $i=>$param) {
			if(!$param->isOptional() && $request->getHeader()->getArgument($param->getName()) == null){
				$response = new Response($request->getSender(), null, -7, 'Invalid arguments provided!');
				$this->registerResponse($response);
				return false;
			}
			$arguments[] = $request->getHeader()->getArgument($param->getName());
		}

		if(!is_subclass_of($controller, 'BaseController')){
			$response = new Response($request->getSender(), null, -8, 'Request could be resolved!');
			$this->registerResponse($response);
			return;
		}
		call_user_func_array(array($controller, $methodName), $arguments);
		$messages = $controller->getMessages();
		foreach($messages as $message){
			$response = new Response($message->getReceiver(), $message->getMessage());
			$this->registerResponse($response);
		}
	}

	private function handleEventResponses(){
		$messages = $this->eventController->getMessages();
		foreach($messages as $message){
			$response = new Response($message->getReceiver(), $message->getMessage());
			$this->registerResponse($response);
		}
	}

	private function registerResponse($response){
		$this->responses[] = $response;
	}

	public function getResponse(){
		if(count($this->responses) > 0){
			$response = array_splice($this->responses, 0, 1);
			return $response[0];
		}
		return null;
	}
}

?>
