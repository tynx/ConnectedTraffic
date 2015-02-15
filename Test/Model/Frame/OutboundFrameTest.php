<?php

namespace ConnectedTrafficTest\Model\Frame;

/*
 *       0                   1                   2                   3
      0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
     +-+-+-+-+-------+-+-------------+-------------------------------+
     |F|R|R|R| opcode|M| Payload len |    Extended payload length    |
     |I|S|S|S|  (4)  |A|     (7)     |             (16/64)           |
     |N|V|V|V|       |S|             |   (if payload len==126/127)   |
     | |1|2|3|       |K|             |                               |
     +-+-+-+-+-------+-+-------------+ - - - - - - - - - - - - - - - +
     |     Extended payload length continued, if payload len == 127  |
     + - - - - - - - - - - - - - - - +-------------------------------+
     |                               |Masking-key, if MASK set to 1  |
     +-------------------------------+-------------------------------+
     | Masking-key (continued)       |          Payload Data         |
     +-------------------------------- - - - - - - - - - - - - - - - +
     :                     Payload Data continued ...                :
     + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
     |                     Payload Data continued ...                |
     +---------------------------------------------------------------+
 */

use \ConnectedTraffic\Model\Frame\Frame as Frame;
use \ConnectedTraffic\Model\Frame\OutboundFrame as OutboundFrame;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

class OutboundFrameTest extends PHPUnit_Framework_TestCase{

	private function generatePayload($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++)
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		return $randomString;
		
	}

	public function testFinFlag(){
		$outOff = new OutboundFrame('somereceiver', $this->generatePayload(1));
		$outOn = new OutboundFrame('somereceiver', $this->generatePayload(1));
		$outOff->setOpcode(Frame::OPCODE_TEXT);
		$outOn->setOpcode(Frame::OPCODE_TEXT);
		$outOn->setIsFin(true);
		// default should be flase
		$this->assertEquals(false, $outOff->isFin());
		$this->assertEquals(true, $outOn->isFin());
		
		
		
	}
}

?>
