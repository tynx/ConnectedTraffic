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

/**
 * This class provides a single method which autogenerates the according
 * upgrade-message for the websocket-protocol.
 */
class Handshake {

	/**
	 * Magic and RFC-defined GUID
	 * http://tools.ietf.org/html/rfc6455#section-1.3
	 */
	const MAGIC_GUID = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

	/**
	 * This parses the header of the request and tries to create the
	 * according response.
	 * @param (string)request the input as string
	 * @return (string) empty string (on fail) or the according upgrade
	 * as string
	 */
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

		if (count($header) === 0 ||
			!isset($header['Sec-WebSocket-Key']) ||
			$header['Sec-WebSocket-Key'] === '') {
			return '';
		}

		$accept = $header['Sec-WebSocket-Key'] . Handshake::MAGIC_GUID;
		$accept = base64_encode(sha1($accept, true));

		$responseParts = array(
			'HTTP/1.1 101 Switching Protocols',
			'Upgrade: websocket',
			'Connection: Upgrade',
			'Sec-WebSocket-Accept: ' . $accept,
		);
		var_dump($responseParts);
		return implode("\r\n", $responseParts) . "\r\n\r\n";
	}
}
