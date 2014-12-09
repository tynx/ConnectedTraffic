<?php

namespace ConnectedTraffic\Model\Request;

class RequestHeader {
	private $time = 0;
	private $length = 0;
	private $action = null;
	private $arguments = array();
	
	public function __construct($time = 0, $length = 0, $action = null) {
		if ($time !== 0) {
			$this->time = $time;
		}
		if ($length !== 0) {
			$this->length = $length;
		}
		if ($action !== null) {
			$this->action = $action;
		}
	}

	public function getTime() {
		return $this->time;
	}

	public function setTime($time) {
		$this->time = $time;
	}

	public function getLength() {
		return $this->length;
	}

	public function setLength($length) {
		$this->length = $length;
	}

	public function getAction() {
		return $this->action;
	}

	public function setAction($action) {
		$this->action = $action;
	}

	public function getArgument($key) {
		if (isset($this->arguments[$key])) {
			return $this->arguments[$key];
		}
		return null;
	}

	public function setArgument($argument, $value) {
		$this->arguments[$argument] = $value;
	}
}
