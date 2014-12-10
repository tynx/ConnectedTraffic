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

use \ConnectedTraffic as ConnectedTraffic;
use \ConnectedTraffic\Controller\ConnectionController
	as ConnectionController;
use \ConnectedTraffic\Model\Frame\Frame as Frame;
use \ConnectedTraffic\Model\Frame\InboundFrame as InboundFrame;
use \ConnectedTraffic\Model\Frame\OutboundFrame as OutboundFrame;

class ConnectedTrafficServer {

	private $masterSocket = null;
	private $controller = null;
	private $config = null;

	// server should be running
	private $running = false;

	// constructor binds to the port/address
	public function __construct() {
		$this->config = ConnectedTraffic::getConfig('server');
		$this->controller = new ConnectionController();
		$msg = 'Starting Server (PID: ' . posix_getpid() . ')';
		ConnectedTraffic::log($msg, 'ConnectedTraffic.Server');
		$this->bind();
	}

	// this function runs forever (socket-server itself)
	public function run() {
		$this->running = true;
		$write = null;
		$except = null;
		while ($this->running) {
			$activeSockets = $this->getActiveSockets();
			$changes = socket_select($activeSockets, $write, $except, 1);
			if ($changes === 0) {
				$this->handleInputOutput();
				continue;
			}
			foreach ($activeSockets as $socket) {
				$connection = $this->controller->getConnectionBySocket($socket);
				if ($socket === $this->masterSocket) {
					$newSocket = socket_accept($this->masterSocket);
					if ($newSocket < 0) {
						ConnectedTraffic::log('socket_accept() failed', 'ConnectedTraffic.Server', 'error');
					} else {
						$this->controller->registerOpen($newSocket);
					}
				} elseif ($connection !== null) {
					$msg = $connection->read();
					if ($msg === null) {
						$connection->close();
						$this->controller->registerClose($connection->getId());
						continue;
					}
					$inFrame = new InboundFrame($connection->getId(), $msg);
					$this->controller->registerInput($inFrame);
					$this->handleInputOutput();
				}
			}
		}
		ConnectedTraffic::log('websocket-server gone!', 'ConnectedTraffic.Server');
	}

	private function handleInputOutput() {
		$this->controller->processInput();
		$outFrame = $this->controller->getOutput();
		while ($outFrame !== null) {
			ConnectedTraffic::log('sending to: ' . $outFrame->getReceiver(), 'ConnectedTraffic.Server');
			$this->controller->getConnectionById($outFrame->getReceiver())->write($outFrame->getData());
			if ($outFrame->getOpcode() === Frame::OPCODE_CLOSE) {
				$this->controller->getConnectionById($outFrame->getReceiver())->close();
				$this->controller->registerClose($outFrame->getReceiver());
			}
			$outFrame = $this->controller->getOutput();
		}
	}

	private function bind() {
		($this->masterSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))                       || die("socket_create() failed");
		socket_set_option($this->masterSocket, SOL_SOCKET, SO_REUSEADDR, 1)                        || die("socket_option() failed");
		socket_bind($this->masterSocket, $this->config['bindAddress'], $this->config['bindPort'])  || die("socket_bind() failed");
		socket_listen($this->masterSocket, 20)                                                     || die("socket_listen() failed");
		ConnectedTraffic::log('Server Started : ' . date('Y-m-d H:i:s'), 'ConnectedTraffic.Server');
		ConnectedTraffic::log('Listening on   : ' . $this->config['bindAddress'] . ':' . $this->config['bindPort'], 'ConnectedTraffic.Server');
	}

	private function getActiveSockets() {
		$sockets = $this->controller->getConnectionSockets();
		$sockets[] = $this->masterSocket;
		return $sockets;
	}
}
