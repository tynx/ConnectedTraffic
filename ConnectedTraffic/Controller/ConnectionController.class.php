<?php

namespace ConnectedTraffic\Controller;

use \ConnectedTraffic as ConnectedTraffic;
use \ConnectedTraffic\Controller\RequestController as RequestController;
use \ConnectedTraffic\Helper\Handshake as Handshake;
use \ConnectedTraffic\Model\Connection as Connection;
use \ConnectedTraffic\Model\Frame\InboundFrame as InboundFrame;
use \ConnectedTraffic\Model\Frame\OutboundFrame as OutboundFrame;

class ConnectionController{

	private $config = null;
	private $inputFrames = array();
	private $outputFrames = array();
	private $connections = array();
	private $requestController = null;
	

	public function __construct(){
		$this->config = ConnectedTraffic::getConfig('server');
		$this->requestController = new RequestController();
	}

	public function registerInput($inFrame){
		ConnectedTraffic::log('registering new input.', 'ConnectedTraffic.Controller.ConnectionController');
		$this->inputFrames[] = $inFrame;
	}

	public function registerOutput($outFrame){
		ConnectedTraffic::log('registering new output.', 'ConnectedTraffic.Controller.ConnectionController');
		$this->outputFrames[] = $outFrame;
	}

	public function registerOpen($socket){
		ConnectedTraffic::log('registering new connection.', 'ConnectedTraffic.Controller.ConnectionController');
		$connection = new Connection($socket);
		$this->connections[] = $connection;
		$this->requestController->addClient($connection->getId());
	}

	public function registerClose($connectionId){
		ConnectedTraffic::log('registering close for connection.', 'ConnectedTraffic.Controller.ConnectionController');
		$this->requestController->onCloseClient($connectionId);
		$this->gatherOutput();
		$this->requestController->removeClient($connectionId);
		foreach($this->connections as $i=>$connection)
			if($connection->getSocket() === null && !$connection->isConnected())
				array_splice($this->connections, $i, 1);
	}

	public function processInput(){
		while(count($this->inputFrames) > 0){
			$inFrame = array_splice($this->inputFrames, 0, 1);
			$this->processFrame($inFrame[0]);
		}
		// ping connections
		foreach($this->connections as $i=>$connection){
			if($connection->getLastIO() + $this->config['pingTimeout'] < time()){
				ConnectedTraffic::log('Pinging client ("' . $connection->getId() . '") due to inactivity.', 'ConnectedTraffic.Controller.ConnectionController');
				$outFrame = new OutboundFrame($connection->getId());
				$outFrame->setOpcode(OutboundFrame::OPCODE_PING);
				$this->registerOutput($outFrame);
			}
		}
	}

	private function processFrame($inFrame){
		$connection = $this->getConnectionById($inFrame->getSender());
		if(!$connection->hasHandshaked()){
			$outFrame = new OutboundFrame($inFrame->getSender(), Handshake::generateResponse($inFrame->getPayload()));
			$outFrame->setIsHandshake();
			$this->registerOutput($outFrame);
			$connection->setHasHandshaked();
			$this->requestController->onConnectClient($inFrame->getSender());
			$this->gatherOutput();
			return;
		}
		if($inFrame->getOpcode() == InboundFrame::OPCODE_CLOSE){
			ConnectedTraffic::log($inFrame->getSender() . ' requested to close connection. sending opcode 8!', 'ConnectedTraffic.Controller.ConnectionController');
			$outFrame = new OutboundFrame($inFrame->getSender());
			$outFrame->setOpcode(OutboundFrame::OPCODE_CLOSE);
			$this->registerOutput($outFrame);
		}
		if($inFrame->getOpcode() == InboundFrame::OPCODE_PING){
			ConnectedTraffic::log('Received "Ping" from client: ' . $inFrame->getSender(), 'ConnectedTraffic.Controller.ConnectionController');
			$outFrame = new OutboundFrame($inFrame->getSender());
			$outFrame->setOpcode(OutboundFrame::OPCODE_PONG);
			$this->registerOutput($outFrame);
		}
		if($inFrame->getOpcode() == InboundFrame::OPCODE_PONG){
			ConnectedTraffic::log('Received "Pong" from client: ' . $inFrame->getSender(), 'ConnectedTraffic.Controller.ConnectionController');
		}
		if($inFrame->getOpcode() == InboundFrame::OPCODE_BINARY){
			ConnectedTraffic::log('recieved unwanted(!) binary-frame from client: ' . $inFrame->getSender(), 'ConnectedTraffic.Controller.ConnectionController');
		}
		if($inFrame->getOpcode() == InboundFrame::OPCODE_TEXT){
			ConnectedTraffic::log('Received Text-message from client: ' . $inFrame->getSender(), 'ConnectedTraffic.Controller.ConnectionController');
			$this->requestController->addRequest($inFrame->getSender(), $inFrame->getPayload());
			$this->requestController->run();
			$this->gatherOutput();
		}
	}

	private function gatherOutput(){
		while(($response = $this->requestController->getResponse()) !== null){
			$outFrame = new OutboundFrame($response->getReceiver(), $response->getRawData());
			$outFrame->setOpcode(OutboundFrame::OPCODE_TEXT);
			$this->registerOutput($outFrame);
		}
	}

	public function getOutput(){
		if(count($this->outputFrames) > 0){
			$outFrame = array_splice($this->outputFrames, 0, 1);
			return $outFrame[0];
		}
		return null;
	}

	public function getConnectionSockets(){
		$sockets = array();
		foreach($this->connections as $connection)
			$sockets[] = $connection->getSocket();
		return $sockets;
	}

	public function getConnectionBySocket($socket){
		foreach($this->connections as $connection){
			if($connection->getSocket()===$socket)
				return $connection;
		}
		return null;
	}

	public function getConnectionById($connectionId){
		foreach($this->connections as $connection){
			if($connection->getId()===$connectionId)
				return $connection;
		}
		return null;
	}
}

?>
