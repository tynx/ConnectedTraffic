<?php

final class Message {
	private $receiver = null;
	private $message = null;

	public function __construct($receiver, $message) {
		$this->receiver = $receiver;
		$this->message = $message;
	}

	public function getMessage() {
		return $this->message;
	}

	public function getReceiver() {
		return $this->receiver;
	}
}
