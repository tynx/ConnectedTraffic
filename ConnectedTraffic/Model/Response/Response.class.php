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

class Response {
	private $receiver = null;
	private $header = null;
	private $body = null;
	private $serializer = null;
	
	public function __construct(
		$receiver,
		$body = null,
		$status = 0,
		$statusMessage = 'OK'
	) {
		$this->receiver = $receiver;
		$this->header = new ResponseHeader(sha1(uniqid() . time()), strlen($body), $status, $statusMessage);
		$this->body = $body;
		//conf based
		$this->serializer = new JSONSerializer($this);
		$this->serializer->serialize();
	}
	
	public function getHeader() {
		return $this->header;
	}
	
	public function getBody() {
		return $this->body;
	}
	
	public function getReceiver() {
		return $this->receiver;
	}
	
	public function getRawData() {
		return $this->serializer->getRawData();
	}
}
