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

namespace ConnectedTraffic\Model\Frame;

class OutboundFrame extends \ConnectedTraffic\Model\Frame\Frame {
	private $receiver = null;

	public function __construct($receiver, $payload = null) {
		$this->receiver = $receiver;
		$this->payload = $payload;
	}

	public function getReceiver() {
		return $this->receiver;
	}

	public function getData() {
		if ($this->isHandshake()) {
			return $this->payload;
		} else {
			return $this->encapsulate();
		}
	}
}
