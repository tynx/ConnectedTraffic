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

class RequestHeader {
	private $time = 0;
	private $length = 0;
	private $tag = null;
	private $contentType = null;
	private $action = null;
	private $arguments = array();
	
	public function __construct(
		$time = 0,
		$length = 0,
		$action = null,
		$contentType = 'text',
		$tag = null
		) {
		if ($time !== 0) {
			$this->time = $time;
		}
		if ($length !== 0) {
			$this->length = $length;
		}
		if ($action !== null) {
			$this->action = $action;
		}
		if(strtolower($contentType) !== 'text'){
			$this->contentType = strtolower($contentType);
		}
		if($tag !== null){
			$this->tag = $tag;
		}
		
	}

	public function getTime() {
		return $this->time;
	}

	public function setTime($time) {
		$this->time = $time;
	}

	public function getLength() {
		return $this->length;
	}

	public function setLength($length) {
		$this->length = $length;
	}

	public function getAction() {
		return $this->action;
	}

	public function setAction($action) {
		$this->action = $action;
	}

	public function getTag() {
		return $this->tag;
	}

	public function setTag($tag) {
		$this->tag = $tag;
	}

	public function getContentType() {
		return $this->contentType;
	}

	public function setContentType($contentType) {
		$this->contentType = strtolower($contentType);
	}

	public function getArgument($key) {
		if (isset($this->arguments[$key])) {
			return $this->arguments[$key];
		}
		return null;
	}

	public function setArgument($argument, $value) {
		$this->arguments[$argument] = $value;
	}
}
