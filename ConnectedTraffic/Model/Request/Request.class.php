<?php

namespace ConnectedTraffic\Model\Request;

class Request {
	protected $id = null;
	protected $sender = null;
	protected $header = null;
	protected $body = null;
	protected $valid = true;
	protected $errorMessage = null;

	public function __construct($sender, $rawData) {
		$this->id = sha1(uniqid() . time());
		$this->sender = $sender;
		//conf-based
		$parser = new JSONParser($rawData);
		if ($parser->parse()) {
			$this->header = $parser->getHeader();
			$this->body = $parser->getBody();
		} else {
			$this->errorMessage = $parser->getErrorMessage();
			$this->valid = false;
		}
	}

	public function getSender() {
		return $this->sender;
	}

	public function getHeader() {
		if ($this->isValid()) {
			return $this->header;
		}
		return null;
	}

	public function getBody() {
		if ($this->isValid()) {
			return $this->body;
		}
		return null;
	}

	public function isValid() {
		return $this->valid;
	}

	public function getErrorMessage() {
		return $this->errorMessage;
	}

}
