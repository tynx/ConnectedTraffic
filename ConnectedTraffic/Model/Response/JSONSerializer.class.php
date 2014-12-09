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

namespace ConnectedTraffic\Model\Response;

class JSONSerializer implements SerializerInterface {
	private $rawData = null;
	private $response = null;
	
	public function __construct($response) {
		$this->response = $response;
	}

	public function serialize() {
		$assocHeader = array();
		$assocHeader['id'] = $this->response->getHeader()->getId();
		$assocHeader['status'] = $this->response->getHeader()->getStatus();
		$assocHeader['header']['statusMessage'] = $this->response->getHeader()->getStatusMessage();
		$assocHeader['server'] = $this->response->getHeader()->getServer();
		$assocHeader['time'] = $this->response->getHeader()->getTime();
		$assocHeader['length'] = $this->response->getHeader()->getLength();
		$this->rawData = '{"header":' . json_encode($assocHeader) . ', "body":"' . str_replace(array('"', '\/'), array('\"', '/'), $this->response->getBody()) . '"}';
	}

	public function getRawData() {
		return $this->rawData;
	}
}
