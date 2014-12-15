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

namespace ConnectedTraffic\Controller;

use \ConnectedTraffic as ConnectedTraffic;
use \ConnectedTraffic\Controller\RequestController as RequestController;
use \ConnectedTraffic\Helper\Handshake as Handshake;
use \ConnectedTraffic\Model\Frame\InboundFrame as InboundFrame;
use \ConnectedTraffic\Model\Frame\OutboundFrame as OutboundFrame;

use \ConnectedTraffic\Controller\Frame\BinaryFrameController
	as BinaryFrameController;
use \ConnectedTraffic\Controller\Frame\CloseFrameController
	as CloseFrameController;
use \ConnectedTraffic\Controller\Frame\PingFrameController
	as PingFrameController;
use \ConnectedTraffic\Controller\Frame\PongFrameController
	as PongFrameController;
use \ConnectedTraffic\Controller\Frame\TextFrameController
	as TextFrameController;

class ConnectionController {

	private $config = null;
	private $inboundFrames = array();
	private $outboundFrames = array();
	private $requestController = null;
	

	public function __construct() {
		$this->config = ConnectedTraffic::config()->getServerConfig();
		//$this->requestController = new RequestController();
	}

	public function registerInput($socket) {
		ConnectedTraffic::log(
			'registering new input.',
			'ConnectedTraffic.Controller.ConnectionController'
		);
		$connection = ConnectedTraffic::getCM()->getConnectionBySocket($socket);
		if ($connection === null) {
			throw new InvalidConnectionException(
				'Given socket was not found in ConnectionController'
			);
		}
		$msg = $connection->read();
		ConnectedTraffic::app()->onMessage($connection->getId(), $msg);
		if ($msg === null) {
			ConnectedTraffic::log(
				'Got empty input. Closing connection.', 
				'ConnectedTraffic.Controller.ConnectionController',
				'warning'
			);
			$connection->close();
			//$this->registerClose($connection->getId());
			return;
		}
		$inFrame = new InboundFrame($connection->getId(), $msg);
		$this->inboundFrames[] = $inFrame;
	}

	public function process(){
		$this->_processInput();
		$this->_handlePing();
		$this->_processOutput();
	}

	public function registerOutput($outFrame){
		$this->outboundFrames[] = $outFrame;
	}

	private function _processInput(){
		$inFrames = $this->inboundFrames;
		$this->inboundFrames = array();
		foreach ($inFrames as $inFrame) {
			$this->_processInboundFrame($inFrame);
		}
	}

	private function _processOutput(){
		$outFrames = $this->outboundFrames;
		$this->outboundFrames = array();
		foreach ($outFrames as $outFrame) {
			$this->_processOutboundFrame($outFrame);
		}
	}

	private function _processInboundFrame($inFrame) {
		$connection = ConnectedTraffic::getCM()->getConnectionById($inFrame->getSender());
		if (!$connection->hasHandshaked()) {
			$connection->setHasHandshaked();
			$outFrame = new OutboundFrame(
				$inFrame->getSender(),
				Handshake::generateResponse($inFrame->getPayload())
			);
			$outFrame->setIsHandshake();
			$this->registerOutput($outFrame);
			ConnectedTraffic::app()->onInitialized($connection->getId());
			return;
		}
		$frameController = null;
		if ($inFrame->getOpcode() === InboundFrame::OPCODE_CLOSE) {
			$frameController = new CloseFrameController();
		}
		if ($inFrame->getOpcode() === InboundFrame::OPCODE_PING) {
			$frameController = new PingFrameController();
		}
		if ($inFrame->getOpcode() === InboundFrame::OPCODE_PONG) {
			$frameController = new PongFrameController();
		}
		if ($inFrame->getOpcode() === InboundFrame::OPCODE_BINARY) {
			$frameController = new BinaryFrameController();
		}
		if ($inFrame->getOpcode() === InboundFrame::OPCODE_TEXT) {
			$frameController = new TextFrameController();
		}
		$frameController->processInboundFrame($inFrame);
		$outboundFrames = $frameController->getOutboundFrames();
		foreach ($outboundFrames as $outFrame){
			$this->registerOutput($outFrame);
		}
	}
	
	private function _processOutboundFrame($outFrame){
		ConnectedTraffic::log('sending to: ' . $outFrame->getReceiver(), 'ConnectedTraffic.ConnectionController');
		ConnectedTraffic::getCM()->getConnectionById($outFrame->getReceiver())->write($outFrame->getData());
		if ($outFrame->getOpcode() === OutboundFrame::OPCODE_CLOSE) {
			ConnectedTraffic::getCM()->getConnectionById($outFrame->getReceiver())->close();
			ConnectedTraffic::app()->onClosed($outFrame->getReceiver());
			//$this->controller->registerClose($outFrame->getReceiver());
		}
	}

	private function _handlePing(){
		$connections = ConnectedTraffic::getCM()->getConnectedConnections();
		$pingTimeLimit = time() - $this->config['pingTimeout'];
		foreach ($connections as $connection) {
			if ($connection->getLastIO() < $pingTimeLimit) {
				ConnectedTraffic::log(
					'Pinging client ("' . $connection->getId() . '")' .
					' due to inactivity.',
					'ConnectedTraffic.Controller.ConnectionController'
				);
				$outFrame = new OutboundFrame($connection->getId());
				$outFrame->setOpcode(OutboundFrame::OPCODE_PING);
				$this->registerOutput($outFrame);
			}
		}
	}

	public function registerOpen($socket) {
		ConnectedTraffic::log('registering new connection.', 'ConnectedTraffic.Controller.ConnectionController');
		ConnectedTraffic::getCM()->registerConnection($socket);
		ConnectedTraffic::app()->onConnected(ConnectedTraffic::getCM()->getConnectionBySocket($socket));
	}
}
