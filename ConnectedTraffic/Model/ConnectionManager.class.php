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

namespace ConnectedTraffic\Model;

class ConnectionManager {
	private $connections = array();

	public function __construct() { }

	public function addConnection($connection){
		$this->connections[] = $connection;
	}

	public function registerConnection($socket){
		$this->addConnection(new Connection($socket));
	}

	public function getConnectionById($connectionId) { 
		foreach ($this->connections as $connection) {
			if ($connection->getId() === $connectionId) {
				return $connection;
			}
		}
		return null;
	}

	public function getConnectionBySocket($socket) {
		foreach ($this->connections as $connection) {
			if ($connection->getSocket() === $socket) {
				return $connection;
			}
		}
		return null;
	}

	public function getSockets() {
		$sockets = array();
		foreach ($this->connections as $connection) {
			$sockets[] = $connection->getSocket();
		}
		return $sockets;
	}

	public function getConnectedSockets(){
		$sockets = array();
		foreach ($this->connections as $connection) {
			if($connection->isConnected())
				$sockets[] = $connection->getSocket();
		}
		return $sockets;
	}

	public function getConnectedConnections(){
		$connections = array();
		foreach ($this->connections as $connection) {
			if($connection->isConnected())
				$connections[] = $connection;
		}
		return $connections;
	}

	public function getClients(){
		$clients = array();
		foreach($this->connections as $connection) {
			$clients[] = new Client($connection->getId());
		}
		return $clients;
	}
}
