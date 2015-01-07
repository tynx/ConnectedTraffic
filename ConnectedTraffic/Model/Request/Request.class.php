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

namespace ConnectedTraffic\Model\Request;
use \ConnectedTraffic as ConnectedTraffic;
use \ConnectedTraffic\Exception\InvalidConfigException as InvalidConfigException;

class Request {
	protected $id = null;
	protected $sender = null;
	protected $header = null;
	protected $rawBody = null;
	protected $body = null;
	protected $valid = true;
	protected $errorMessage = null;

	public function __construct($sender, $rawData) {
		$this->id = sha1(uniqid() . time());
		$this->sender = $sender;
		$type = ConnectedTraffic::config()->getServerConfig('protocolFormat');
		$className = '\\ConnectedTraffic\\Model\\Request\\' . $type . 'Parser';
		if(class_exists($className, false)){
			$parser = new $className($rawData);
		}else{
			throw new InvalidConfigException('Not implemented parser: ' . $className);
		}
		
		if ($parser->parse()) {
			$this->header = $parser->getHeader();
			$this->rawBody = $parser->getBody();
			if($this->header->getContentType() === 'text'){
				$this->body = $this->rawBody;
			} elseif($this->header->getContentType() === 'json'){
				$this->body = json_decode($this->rawBody, true);
			}
		} else {
			$this->errorMessage = $parser->getErrorMessage();
			$this->valid = false;
		}
	}

	public function getSender() {
		return $this->sender;
	}

	public function getHeader() {
		if ($this->isValid()) {
			return $this->header;
		}
		return null;
	}

	public function getRawBody() {
		if ($this->isValid()) {
			return $this->rawBody;
		}
		return null;
	}

	public function getBody() {
		if ($this->isValid()) {
			return $this->body;
		}
		return null;
	}

	public function isValid() {
		return $this->valid;
	}

	public function getErrorMessage() {
		return $this->errorMessage;
	}

}
