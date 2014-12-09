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

// handles the demasking of a frame
class Masking {

	const MAX_KEY_NUMBER = 4;
	private $keys = array('', '', '', '');

	public function __construct($keys = NULL) {
		if (is_array($keys) && count($keys) === self::MAX_KEY_NUMBER) {
			$this->keys = $keys;
		}
	}

	public function unmaskBytes($masked) {
		$unmasked = '';
		for ($i = 0; $i < strlen($masked); $i++) {
			$xor = ord($masked[$i]) ^ ord($this->keys[($i % self::MAX_KEY_NUMBER)]);
			$unmasked .= urldecode('%' . dechex($xor));
		}
		return $unmasked;
	}
}
