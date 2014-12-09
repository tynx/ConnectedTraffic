<?php

class Client {
	private $connectionId = null;
	private $customValues = array();
	
	public function __construct($connectionId) {
		$this->connectionId = $connectionId;
	}

	public function getConnectionId() {
		return $this->connectionId;
	}

	public function setCustomValue($key, $value) {
		if ($key !== null) {
			$this->customValues[$key] = $value;
		}
	}

	public function getCustomValue($key) {
		if (isset($this->customValues[$key])) {
			return $this->customValues[$key];
		}
		return null;
	}
}
