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

namespace ConnectedTraffic\Controller\Frame;

use \ConnectedTraffic as ConnectedTraffic;
use \ConnectedTraffic\Model\Frame\InboundFrame as InboundFrame;
use \ConnectedTraffic\Model\Frame\OutboundFrame as OutboundFrame;
use \ConnectedTraffic\Model\Request\Request as Request;

class TextFrameController 
	extends \ConnectedTraffic\Controller\Frame\FrameController {

	public function processInboundFrame(InboundFrame $inFrame){
		ConnectedTraffic::log(
			'Got textframe => controller',
			'ConnectedTraffic.Controller.Frame.TextFrameController'
		);
		$request = new Request(
			$inFrame->getSender(),
			$inFrame->getPayload()
		);

		ConnectedTraffic::app()->processRequest($request);
	}
}
