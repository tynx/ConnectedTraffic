<?php

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
