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
		
		if(strpos($rawData, 'GET / HTTP/1.1') === 0){
			// No need to parse! It's the handshake. Probably. But NOT
			// a frame.
			$this->setIsHandshake();
			$this->payload = $rawData;
			return;
		}
		
		$rawBytes = unpack('C*', $rawData);
		$this->header['fin'] = ($rawBytes[1] & (1<<7)) ? true : false;
		$this->header['rsv1'] = ($rawBytes[1] & (1<<6)) ? true : false;
		$this->header['rsv2'] = ($rawBytes[1] & (1<<5)) ? true : false;
		$this->header['rsv3'] = ($rawBytes[1] & (1<<4)) ? true : false;
		$this->header['opcode'] = $rawBytes[1] & 0xF;
		$this->header['masked'] = ($rawBytes[2] & (1<<7)) ? true : false;
		$this->header['length'] = (float)($rawBytes[2] & 0x7F);

		if ($this->header['length'] === (float)127) {
			// we do not supported frames with more payload with  >65536
			// length!
		}
		if ($this->header['length'] === (float)126) {
			$this->header['length'] = $rawBytes[3] << 8 | $rawBytes[4];
		}

		if($this->header['masked'] === true){
			$masking = null;
			$payloadPosition = 0;
			if($this->header['length'] > 125){
				$masking = new Masking(array_slice($rawBytes, 4, 4));
				$payloadPosition = 8;
			}elseif($this->header['length'] > 0){
				$masking = new Masking(array_slice($rawBytes, 2, 4));
				$payloadPosition = 6;
			}
			if($masking !== null){
				$this->payload = $masking->unmaskBytes(array_slice($rawBytes, $payloadPosition));
			}
		}else{
			if($this->header['length'] > 125){
				$this->payload = substr($rawData, 4);
			}elseif($this->header['length'] > 0){
				$this->payload = substr($rawData, 2);
			}
			
		}
	}

	protected function encapsulate() {
		$header = 0;
		$header = ($this->header['fin']) ? $header | (1<<15) : $header;
		$header = ($this->header['rsv1']) ? $header | (1<<14) : $header;
		$header = ($this->header['rsv2']) ? $header | (1<<13) : $header;
		$header = ($this->header['rsv3']) ? $header | (1<<12) : $header;
		$header = $header | ($this->header['opcode'] << 8);
		$header = ($this->header['masked']) ? $header | (1<<7) : $header;
		
		
		if(strlen($this->payload) >= 65535){
			$header = ($header | 127) << 64;
		} elseif (strlen($this->payload) >= 126){
			$header = ($header | 126) << 16;
		}
		$header = $header | strlen($this->payload);
		$rawData = '';
		$bytes = unpack("C*", pack("L", $header));
		foreach($bytes as $byte){
			$rawData = chr($byte) . $rawData;
		}

		for ($i = 0; $i < strlen($this->payload); $i++) {
			$rawData .= utf8_decode($this->payload[$i]);
		}

		return $rawData;
	}
}
