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
