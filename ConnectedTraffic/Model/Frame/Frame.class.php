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

use \ConnectedTraffic\Helper\Masking as Masking;

// a websocket-frame
abstract class Frame {

	const OPCODE_CONTINUATION = 0;
	const OPCODE_TEXT = 1;
	const OPCODE_BINARY = 2;
	const OPCODE_CLOSE = 8;
	const OPCODE_PING = 9;
	const OPCODE_PONG = 10;

	protected $header = array(
		'fin'    => true,
		'rsv1'   => false,
		'rsv2'   => false,
		'rsv3'   => false,
		'opcode' => 0,
		'masked' => false,
		'length' => 0,
	);

	protected $payload = null;
	protected $isHandshake = false;

	public function setIsHandshake() {
		$this->isHandshake = true;
	}

	public function isHandshake() {
		return $this->isHandshake;
	}

	public function getOpcode() {
		return $this->header['opcode'];
	}

	public function setOpcode($opcode) {
		$this->header['opcode'] = $opcode;
	}

	public function getPayload() {
		return $this->payload;
	}

	protected function parse($rawData) {
		$bits = decbin(ord($rawData[0])) . decbin(ord($rawData[1]));

		$this->header['fin'] = ($bits[0] === '1') ? true : false;
		$this->header['rsv1'] = ($bits[1] === '1') ? true : false;
		$this->header['rsv2'] = ($bits[2] === '1') ? true : false;
		$this->header['rsv3'] = ($bits[3] === '1') ? true : false;
		$this->header['opcode'] = bindec(substr($bits, 4, 4));
		$this->header['masked'] = ($bits[8] === '1') ? true : false;
		$this->header['length'] = bindec(substr($bits, 9));

		$rawData = substr($rawData, 2);

		if ($this->header['length'] === 127) {
			$bits = decbin(ord($rawData[0])) . decbin(ord($rawData[1])) .
			decbin(ord($rawData[2])) . decbin(ord($rawData[3])) .
			decbin(ord($rawData[4])) . decbin(ord($rawData[5])) .
			decbin(ord($rawData[6])) . decbin(ord($rawData[7]));
			$this->header['length'] = bindec($bits);
			$rawData = substr($rawData, 8);
		}

		if ($this->header['length'] === 126) {
			$this->header['length'] = bindec(decbin(ord($rawData[0])) . decbin(ord($rawData[1])));
			$rawData = substr($rawData, 2);
		}

		
		if ($this->header['masked'] === true) {
			$masking = new Masking(array(
				$rawData[0], $rawData[1], $rawData[2], $rawData[3]
			));
			$rawData = substr($rawData, 4);
			$rawData = $masking->unmaskBytes($rawData);
		}
		$this->payload = $rawData;
	}

	protected function encapsulate() {
		$rawData = '';
		$bits = (($this->header['fin'] === true) ? '1' : '0') . '000';

		$bits .= str_pad(decbin($this->getOpcode()), 4, '0', STR_PAD_LEFT) . '0';
		$lengthBits = decbin(strlen($this->payload));

		if (strlen($this->payload) >= 65535) {
			$bits .= '1111111' . str_pad($lengthBits, 64, '0', STR_PAD_LEFT);
		} elseif (strlen($this->payload) >= 126) {
			$bits .= '1111110' . str_pad($lengthBits, 16, '0', STR_PAD_LEFT);
		} else {
			$bits .= str_pad($lengthBits, 7, '0', STR_PAD_LEFT);
		}

		for ($i = 0; $i < (strlen($bits) / 8); $i++) {
			$rawData .= chr(bindec(substr($bits, $i * 8, 8)));
		}

		for ($i = 0; $i < strlen($this->payload); $i++) {
			$rawData .= utf8_decode($this->payload[$i]);
		}

		return $rawData;
	}
}
