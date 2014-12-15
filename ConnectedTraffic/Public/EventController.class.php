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

use \ConnectedTraffic\Model\Response\Response as Response;

abstract class EventController {
	private $responses = array();
	protected $clients = array();
	protected $sender = null;
	protected $senderId = null;

	public final function __construct($clients, $senderId) {
		$this->clients = $clients;
		$this->senderId = $senderId;
		foreach ($clients as $client) {
			if ($client->getConnectionId() === $senderId) {
				$this->sender = $client;
			}
		}
	}

	public abstract function onConnected();
	public abstract function onInitialized();
	public abstract function onReceived($data);
	public abstract function onSent($data);
	public abstract function onClosed();

	protected final function addResponse($body, $connectionId = null, $status = 0, $statusMessage = 'OK') {
		if($connectionId === null || $this->sender !== null)
			$connectionId = $this->sender->getConnectionId();
		if($connectionId === null)
			$connetionId = $this->senderId;
		$this->responses[] = new Response($connectionId, $body, $status, $statusMessage);
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

	public final function getResponses() {
		return $this->responses;
	}
}
