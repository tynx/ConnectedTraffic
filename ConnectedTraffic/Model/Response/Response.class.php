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
use \ConnectedTraffic as ConnectedTraffic;
use \ConnectedTraffic\Exception\InvalidConfigException as InvalidConfigException;

class Response {
	private $receiver = null;
	private $header = null;
	private $rawBody = null;
	private $body = null;
	private $serializer = null;
	
	public function __construct(
		$receiver,
		$tag = null,
		$contentType = 'text',
		$body = null,
		$status = 0,
		$statusMessage = 'OK'
	) {
		$this->receiver = $receiver;
		$id = sha1(uniqid() . time());
		$this->header = new ResponseHeader(
			$id,
			$tag,
			$contentType,
			strlen($body),
			$status,
			$statusMessage
		);
		$this->rawBody = $body;
		if($this->header->getContentType() === 'text'){
			$this->body = $this->rawBody;
		}elseif($this->header->getContentType() === 'json'){
			$search = array('"', '\/');
			$replace = array('\"', '/');
			$this->body = str_replace($search, $replace, $this->rawBody);
		}
		$type = ConnectedTraffic::config()->getServerConfig('protocolFormat');
		$className = '\\ConnectedTraffic\\Model\\Response\\' . $type . 'Serializer';
		if(class_exists($className, false)){
			$this->serializer = new $className($this);
		}else{
			throw new InvalidConfigException('Not implemented parser: ' . $className);
		}
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
