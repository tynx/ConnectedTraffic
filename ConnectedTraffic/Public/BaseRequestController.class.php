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
		$this->messages[] = new Message(
			$this->sender->getConnectionId(),
			$message
		);
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
