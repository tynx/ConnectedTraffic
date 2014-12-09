<?php

abstract class BaseController {
	private $messages = array();
	protected $clients = array();
	protected $sender = null;
	protected $requestHeader = null;
	protected $body = null;

	public final function __construct(
		$clients,
		$sender,
		$requestHeader,
		$body
	) {
		$this->clients = $clients;
		foreach ($clients as $client) {
			if ($client->getConnectionId() === $sender) {
				$this->sender = $client;
			}
		}
		$this->requestHeader = $requestHeader;
		$this->body = $body;
	}

	protected final function addResponse($message) {
		$this->messages[] = new Message($this->sender->getConnectionId(), $message);
	}

	protected final function addMessage($receiver, $message) {
		$this->messages[] = new Message($receiver, $message);
	}

	protected final function findClientsByValue($key, $value) {
		$resultList = array();
		foreach ($this->clients as $client) {
			if ($client->getCustomValue($key) === $value) {
				$resultList[] = $client;
			}
		}
		if (count($resultList) === 0) {
			return null;
		}
		return $resultList;
	}

	protected final function findClientByValue($key, $value) {
		$resultList = $this->findClientsByValue($key, $value);
		if (count($resultList) === 0) {
			return null;
		}
		return $resultList[0];
	}

	public final function getMessages() {
		return $this->messages;
	}
}
