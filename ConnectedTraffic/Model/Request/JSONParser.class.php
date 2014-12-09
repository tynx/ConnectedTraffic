<?php

namespace ConnectedTraffic\Model\Request;

class JSONParser implements ParserInterface {

	private $jsonInAssoc = array();
	private $errorMessage = null;
	private $header = null;
	private $body = null;

	public function __construct($rawData) {
		$this->header = new RequestHeader();
		$this->jsonInAssoc = json_decode($rawData, true);
	}

	public function parse() {
		if ($this->jsonInAssoc === null) {
			$this->errorMessage = 'Did not receive valid JSON-Request.';
			return false;
		}

		if (!isset($this->jsonInAssoc['header'])) {
			$this->errorMessage = 'Did not receive valid Request. Header missing.';
			return false;
		}

		$header = $this->jsonInAssoc['header'];

		if (!isset($header['action'])) {
			$this->errorMessage = 'Did not receive valid Request. Action missing.';
			return false;
		}

		$this->header->setAction($header['action']);
		
		if (isset($header['time'])) {
			$this->header->setTime($header['time']);
		}

		if (isset($header['length'])) {
			$this->header->setLength($header['length']);
		}

		if (isset($header['arguments']) && is_array($header['arguments'])) {
			foreach ($header['arguments'] as $argument => $value) {
				$this->header->setArgument($argument, $value);
			}
		}

		if (isset($this->jsonInAssoc['body']) && $this->jsonInAssoc['body'] !== null) {
			$this->body = $this->jsonInAssoc['body'];
		}
		return true;
	}

	public function getHeader() {
		return $this->header;
	}

	public function getBody() {
		return $this->body;
	}

	public function getErrorMessage() {
		return $this->errorMessage;
	}
}
