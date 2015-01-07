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
		$objHeader = $this->response->getHeader();
		$assocHeader['id'] = $objHeader->getId();
		$assocHeader['tag'] = $objHeader->getTag();
		$assocHeader['contentType'] = $objHeader->getContentType();
		$assocHeader['status'] = $objHeader->getStatus();
		$assocHeader['statusMessage'] = $objHeader->getStatusMessage();
		$assocHeader['server'] = $objHeader->getServer();
		$assocHeader['time'] = $objHeader->getTime();
		$assocHeader['length'] = $objHeader->getLength();
		
		$body = $this->response->getBody();
		$this->rawData = '{"header":' . json_encode($assocHeader) . 
			', "body":"' . $body . '"}';
	}

	public function getRawData() {
		return $this->rawData;
	}
}
