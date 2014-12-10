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

namespace ConnectedTraffic;

use \ConnectedTraffic as ConnectedTraffic;
use \ConnectedTraffic\Model\Response\Response as Response;

class ConnectedTrafficApp {
	
	private $responses = array();
	
	public function onConnected($connectionId){
		echo "APP: connected event!\n";
	}
	public function onInitialized($connectionId){
		echo "APP: initialized event!\n";
	}
	public function onMessage($connectionId, $data){
		echo "APP: message event!\n";
		echo "====\n" . $data . "\n====\n";
	}
	public function onClosed($connectionId){
		echo "APP: closed event!\n";
	}
	public function processRequest($connectionId, $request){
		echo "APP: processing request!\n";
		echo "====\n";
		//$request->getBody();
		$this->responses[] = new Response($connectionId, "haba");
		echo "\n====\n";
	}
	public function getResponse(){
		$response = null;
		if(count($this->responses) >0){
			$response = array_splice($this->responses, 0, 1);
			$response = $response[0];
		}
		return $response;
	}
}
