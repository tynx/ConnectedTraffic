<?php

abstract class BaseEventController {

	private $messages = array();

	public final function __construct() { }

	public abstract function onConnect($clients, $connectionId);
	
	public abstract function onClose($clients, $connectionId);

	protected final function addMessage($receiver, $message) {
		$this->messages[] = new Message($receiver, $message);
	}

	public final function getMessages() {
		$messages = $this->messages;
		$this->messages = array();
		return $messages;
	}
}
