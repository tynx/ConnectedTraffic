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

// READ(!!): http://tools.ietf.org/html/rfc6455#section-5.5.3

namespace ConnectedTraffic\Helper;

class Handshake {

	const MAGIC_GUID = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

	public static function generateResponse($request) {

		$header = array();
		$headerLines = explode("\r\n", $request);
		foreach ($headerLines as $headerLine) {
			$headerParts = explode(': ', $headerLine);
			if (count($headerParts) !== 2) {
				continue;
			}
			$header[$headerParts[0]] = $headerParts[1];
		}

		if (count($header) < 1 || !isset($header['Sec-WebSocket-Key'])
			|| $header['Sec-WebSocket-Key'] === '') {
			return '';
		}

		$accept = base64_encode(sha1($header['Sec-WebSocket-Key'] . Handshake::MAGIC_GUID, true));

		$responseParts = array(
			'HTTP/1.1 101 Switching Protocols',
			'Upgrade: websocket',
			'Connection: Upgrade',
			'Sec-WebSocket-Accept: ' . $accept,
		);

		return implode("\r\n", $responseParts) . "\r\n\r\n";
	}
}
