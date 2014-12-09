<?php

namespace ConnectedTraffic\Model\Response;

use \ConnectedTraffic\Component\Config as Config;

class ResponseHeader {
	private $id = null;
	private $status = 0;
	private $statusMessage = null;
	private $server = null;
	private $time = 0;
	private $length = 0;

	public function __construct($id, $length, $status, $statusMessage) {
		$this->id = $id;
		$this->status = $status;
		$this->statusMessage = $statusMessage;
		$this->time = round(microtime(true) * 1000); // javascript parseable time
		$this->length = $length;
		$this->server = SERVER_NAME . ' (' . SERVER_VERSION . ') at ' . SERVER_IP;
	}

	public function getId() {
		return $this->id;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getStatusMessage() {
		return $this->statusMessage;
	}

	public function getServer() {
		return $this->server;
	}

	public function getTime() {
		return $this->time;
	}

	public function getLength() {
		return $this->length;
	}
}
