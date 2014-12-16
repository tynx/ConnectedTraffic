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

namespace ConnectedTraffic\Model;

class Connection {
	private $id = null;
	private $socket = null;
	private $connected = false;
	private $handshaked = false;
	private $lastIO = 0;
	private $connectTime = 0;

	public function __construct($socket) {
		$this->id = sha1(uniqid() . time());
		$this->socket = $socket;
		if ($socket !== null) {
			$this->connected = true;
			$this->connectTime = time();
			$this->lastIO = time();
		}
	}

	public function isConnected() {
		return $this->connected;
	}

	public function setHasHandshaked() {
		$this->handshaked = true;
	}

	public function hasHandshaked() {
		return $this->handshaked;
	}

	public function getId() {
		return $this->id;
	}

	public function getSocket() {
		return $this->socket;
	}

	public function read() {
		$this->lastIO = time();
		$buffer = '';
		socket_recv($this->socket, $buffer, 10240 * 10, 0);
		return $buffer;
		/*$message = '';
		$buffer = '';
		while(@socket_recv($this->socket, $buffer, 10240)){
			if($buffer != null)
				$message .= $buffer;
			if($message === '' && $buffer === null)
				return null;
		}
		return $message;*/
	}

	public function write($message) {
		$this->lastIO = time();
		$bytes = socket_write($this->socket, $message, strlen($message));
		if ($bytes !== false) {
			return true;
		}
		return false;
	}

	public function getLastIO() {
		return $this->lastIO;
	}

	public function close() {
		// no warning, because of bad client implementation in browsers
		// like chrome.
		@socket_shutdown($this->socket, 2);
		@socket_close($this->socket);
		$this->socket = null;
		$this->connected = false;
	}
}
