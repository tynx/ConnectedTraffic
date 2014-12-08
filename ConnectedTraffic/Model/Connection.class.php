<?php

namespace ConnectedTraffic\Model;

class Connection{
	private $id = null;
	private $socket = null;
	private $connected = false;
	private $handshaked = false;
	private $lastIO = 0;
	private $connectTime = 0;

	public function __construct($socket){
		$this->id = sha1(uniqid() . time());
		$this->socket = $socket;
		if($socket !== null){
			$this->connected = true;
			$this->connectTime = time();
		}
	}

	public function isConnected(){
		return $this->connected;
	}

	public function setHasHandshaked(){
		$this->handshaked = true;
	}

	public function hasHandshaked(){
		return $this->handshaked;
	}

	public function getId(){
		return $this->id;
	}

	public function getSocket(){
		return $this->socket;
	}

	public function read(){
		$this->lastIO = time();
		$buffer = '';
		$bytes = socket_recv($this->socket,$buffer,10240*10,0);
		return $buffer;
		$message = '';
		$buffer = '';
		while(@socket_recv($this->socket, $buffer, 10240)){
			if($buffer != null)
				$message .= $buffer;
			if($message === '' && $buffer === null)
				return null;
		}
		return $message;
	}

	public function write($message){
		$this->lastIO = time();
		if(socket_write($this->socket,$message,strlen($message)) !== false)
			return true;
		return false;
	}

	public function getLastIO(){
		return $this->lastIO;
	}

	public function close(){
		socket_shutdown($this->socket, 2);
		socket_close($this->socket);
		$this->socket = null;
		$this->connected = false;
	}
}

?>
