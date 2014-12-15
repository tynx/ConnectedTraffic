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
