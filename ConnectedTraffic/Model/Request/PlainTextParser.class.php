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

class PlainTextParser implements ParserInterface {

	private $plainText = null;
	private $errorMessage = null;
	private $header = null;
	private $body = null;

	public function __construct($rawData) {
		$this->header = new RequestHeader();
		$this->plainText = urldecode($rawData);
	}

	public function parse() {
		if ($this->plainText === null) {
			$this->errorMessage = 'Did not receive any data.';
			return false;
		}

		$headerFields = array();
		$this->body = '';
		$lines = explode("\n", $this->plainText);
		$parsingHeader = true;
		foreach($lines as $line){
			if($line === ''){
				$parsingHeader = false;
				continue;
			}
			if($parsingHeader === false){
				$this->body .= $line . "\n";
				continue;
			}
			$valuePosition = strpos($line, ': ');
			if($valuePosition === false){
				// We ignore the line...
			} elseif (strpos($line, 'action') === 0){
				$headerFields['action'] = substr($line, $valuePosition+2);
			} elseif (strpos($line, 'time') === 0){
				$headerFields['time'] = substr($line, $valuePosition+2);
			} elseif (strpos($line, 'length') === 0){
				$headerFields['length'] = substr($line, $valuePosition+2);
			} elseif (strpos($line, 'tag') === 0){
				$headerFields['tag'] = substr($line, $valuePosition+2);
			} elseif (strpos($line, 'contentType') === 0){
				$headerFields['contentType'] = substr($line, $valuePosition+2);
			}  elseif (strpos($line, 'arguments') === 0){
				$headerFields['arguments'] = explode(',', substr($line, $valuePosition+2));
			}
		}
		if(strlen($this->body)>1){
			$this->body = substr($this->body, 0, -1);
		}
		
		if (!isset($headerFields['action'])) {
			$this->errorMessage = 'Did not receive valid Request. ' .
			'Action missing.';
			return false;
		}

		$parts = explode('/', $headerFields['action']);
		
		if (count($parts) !== 2) {
			$this->errorMessage = 'Did not receive valid Request. ' . 
			'Action format is invalid.';
			return false;
		}

		if (empty($parts[0]) || empty($parts[1])) {
			$this->errorMessage = 'Did not receive valid Request. ' . 
			'Action format is invalid.';
			return false;
		}

		$this->header->setAction($headerFields['action']);
		
		if (isset($headerFields['time'])) {
			$this->header->setTime($headerFields['time']);
		}

		if (isset($headerFields['length'])) {
			$this->header->setLength($headerFields['length']);
		}

		if (isset($headerFields['tag'])) {
			$this->header->setTag($headerFields['tag']);
		}

		if (isset($headerFields['contentType'])) {
			$this->header->setLength($headerFields['contentType']);
		}

		if (isset($headerFields['arguments']) &&
			is_array($headerFields['arguments'])) {
			foreach ($headerFields['arguments'] as $value) {
				$parts = explode('=', $value);
				if(count($parts)!== 2){
					continue;
				}
				$this->header->setArgument($parts[0], $parts[1]);
			}
		}
		return true;
	}

	public function getHeader() {
		return $this->header;
	}

	public function getBody() {
		return $this->body;
	}

	public function getErrorMessage() {
		return $this->errorMessage;
	}
}
