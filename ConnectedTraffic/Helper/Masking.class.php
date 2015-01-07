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

namespace ConnectedTraffic\Helper;

use ConnectedTraffic\Exception\InvalidParameterException
	as InvalidParameterException;

/**
 * This class provides an easy way to demask a frame. Currently there is
 * no support for masking frames, as this is not really needed as the
 * server is not supposed to mask frames. But may be needed in future...
 * http://tools.ietf.org/html/rfc6455#section-5.3
 */
class Masking {

	/**
	 * 4 Bytes are used for masking (=>32bit)
	 */
	const KEY_LENGTH = 4;

	/**
	 * The 4 bytes itself
	 */
	private $keyBytes = array(0,0,0,0);

	/**
	 * We need the masking-keys to mask/demask a frame. It is always
	 * 4bytes. This class expects an array of 4 Strings or chars. 
	 * @params (array)keys the keys which should be used to demask
	 */
	public function __construct($keys) {
		if (!is_array($keys) || count($keys) !== self::KEY_LENGTH) {
			throw new InvalidParameterException(
				'Invalid masking-key provided.'
			);
		}
		$this->keys = $keys;
	}

	/**
	 * This method unmasks given bytes with the masking keys provided in
	 * while instanciating this object.
	 * @param (String)masked the bytes to unmask
	 * @return (String) the unsmasked bytes
	 */
	public function unmaskBytes($masked) {
		if (!is_array($masked)) {
			throw new InvalidParameterException(
				'Invalid type in maskedBytes. byte-array expected.'
			);
		}
		$unmasked = '';
		for ($i = 0; $i < count($masked); $i++) {
			$xor = $masked[$i] ^ $this->keys[($i % self::KEY_LENGTH)];
			$unmasked .= urldecode('%' . dechex($xor));
		}
		return $unmasked;
	}
}
