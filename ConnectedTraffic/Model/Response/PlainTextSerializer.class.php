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

class PlainTextSerializer implements SerializerInterface {
	private $rawData = null;
	private $response = null;
	
	public function __construct($response) {
		$this->response = $response;
	}

	public function serialize() {
		$assocHeader = array();
		$objHeader = $this->response->getHeader();
		$this->rawData = 'id: ' . $objHeader->getId() . "\n";
		$this->rawData .= 'tag: ' . $objHeader->getTag() . "\n";
		$this->rawData .= 'contentType: ' . $objHeader->getContentType() . "\n";
		$this->rawData .= 'status: ' . $objHeader->getStatus() . "\n";
		$this->rawData .= 'statusMessage: ' . $objHeader->getStatusMessage() . "\n";
		$this->rawData .= 'server: ' . $objHeader->getServer() . "\n";
		$this->rawData .= 'time: ' . $objHeader->getTime() . "\n";
		$this->rawData .= 'length: ' . $objHeader->getLength() . "\n";
		$this->rawData .= "\n" . $this->response->getBody();
	}

	public function getRawData() {
		return $this->rawData;
	}
}
