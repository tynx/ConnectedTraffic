<?php

namespace ConnectedTraffic\Helper;

// handles the demasking of a frame
class Masking {

	const MAX_KEY_NUMBER = 4;
	private $keys = array('', '', '', '');

	public function __construct($keys = NULL){
		if($keys !== NULL && is_array($keys) && count($keys) == self::MAX_KEY_NUMBER)
			$this->keys = $keys;
	}

	public function unmaskBytes($masked){
		$unmasked = '';
		for($i=0; $i<strlen($masked); $i++){
			$xor = ord($masked[$i]) ^ ord($this->keys[($i%self::MAX_KEY_NUMBER)]);
			$unmasked .= urldecode('%' . dechex($xor));
		}
		return $unmasked;
	}
}
