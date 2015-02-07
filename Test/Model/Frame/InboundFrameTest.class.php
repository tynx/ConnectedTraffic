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

use \ConnectedTraffic\Model\Frame\InboundFrame as InboundFrame;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

class InboundFrameTest extends PHPUnit_Framework_TestCase{

	private function generatePayload($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++)
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		return $randomString;
	}

	public function testFinFlag(){
		$binaryOn = chr(bindec('10000001')) . chr(bindec('00000001')) . 'a';
		$binaryOff = chr(bindec('00000001')) . chr(bindec('00000001')) . 'a';
		$inOn = new InboundFrame('somesender', $binaryOn);
		$inOff = new InboundFrame('somesender', $binaryOff);

		// fin flag
		$this->assertEquals(true, $inOn->isFin());
		$this->assertEquals(false, $inOff->isFin());

		// Test validator
		$this->assertEquals(true, $inOn->isValid());
		$this->assertEquals(true, $inOff->isValid());
	}

	public function testRSVFlags(){
		$binaryOn1 = chr(bindec('11000001')) . chr(bindec('00000001')) . 'a';
		$binaryOn2 = chr(bindec('10100001')) . chr(bindec('00000001')) . 'a';
		$binaryOn3 = chr(bindec('10010001')) . chr(bindec('00000001')) . 'a';
		$binaryOff = chr(bindec('10000001')) . chr(bindec('00000001')) . 'a';
		$inOn1 = new InboundFrame('somesender', $binaryOn1);
		$inOn2 = new InboundFrame('somesender', $binaryOn2);
		$inOn3 = new InboundFrame('somesender', $binaryOn3);
		$inOff = new InboundFrame('somesender', $binaryOff);

		// RSV1
		$this->assertEquals(true, $inOn1->isRSV1());
		$this->assertEquals(false, $inOn1->isRSV2());
		$this->assertEquals(false, $inOn1->isRSV3());

		// RSV2
		$this->assertEquals(false, $inOn2->isRSV1());
		$this->assertEquals(true, $inOn2->isRSV2());
		$this->assertEquals(false, $inOn2->isRSV3());

		// RSV3
		$this->assertEquals(false, $inOn3->isRSV1());
		$this->assertEquals(false, $inOn3->isRSV2());
		$this->assertEquals(true, $inOn3->isRSV3());

		// All off
		$this->assertEquals(false, $inOff->isRSV1());
		$this->assertEquals(false, $inOff->isRSV2());
		$this->assertEquals(false, $inOff->isRSV3());

		// Test validator
		$this->assertEquals(false, $inOn1->isValid());
		$this->assertEquals(false, $inOn2->isValid());
		$this->assertEquals(false, $inOn3->isValid());
		$this->assertEquals(true, $inOff->isValid());
	}

	// TEST INVALID KEY!
	public function testMasking(){
		$key = chr(45) . chr(189) . chr(98) . chr(211);
		$message = 'Hello World!'; // 12 chars => 0001100
		// following line represents same message, but masked with $key
		$maskedMessage = chr(101) . chr(216) . chr(14) . chr(191) . chr(66) . chr(157) . chr(53) . chr(188) . chr(95) . chr(209) . chr(6) . chr(242);
		$masking = chr(bindec('10000001')) . chr(bindec('10001100')) . $key . $maskedMessage;
		$raw = chr(bindec('10000001')) . chr(bindec('00001100')) . $message;
		$inMasking = new InboundFrame('somesender', $masking);
		$inRaw = new InboundFrame('somesender', $raw);

		// Test if messages are ok
		$this->assertEquals($message, $inRaw->getPayload());
		$this->assertEquals($message, $inMasking->getPayload());

		// Test validator
		$this->assertEquals(true, $inRaw->isValid());
		$this->assertEquals(true, $inMasking->isValid());
	}

	public function testLength(){
		$raw1 = chr(bindec('10000001')) . chr(bindec('00000001')) . $this->generatePayload(1);
		$raw10 = chr(bindec('10000001')) . chr(bindec('00001010')) . $this->generatePayload(10);
		$raw100 = chr(bindec('10000001')) . chr(bindec('01100100')) . $this->generatePayload(100);
		$raw125 = chr(bindec('10000001')) . chr(bindec('01111101')) . $this->generatePayload(125);
		$raw126 = chr(bindec('10000001')) . chr(bindec('01111110')) . chr(0) . chr(126) . $this->generatePayload(126);
		$raw127 = chr(bindec('10000001')) . chr(bindec('01111110')) . chr(0) . chr(127) . $this->generatePayload(127);
		$raw128 = chr(bindec('10000001')) . chr(bindec('01111110')) . chr(0) . chr(128) . $this->generatePayload(128);
		$raw1000 = chr(bindec('10000001')) . chr(bindec('01111110')) . chr(bindec('00000011')) . chr(bindec('11101000')) . $this->generatePayload(1000);
		$raw10000 = chr(bindec('10000001')) . chr(bindec('01111110')) . chr(bindec('00100111')) . chr(bindec('00010000')) . $this->generatePayload(10000);
		$raw65534 = chr(bindec('10000001')) . chr(bindec('01111110')) . chr(bindec('11111111')) . chr(bindec('11111110')) . $this->generatePayload(65534);
		$raw65535 = chr(bindec('10000001')) . chr(bindec('01111110')) . chr(bindec('11111111')) . chr(bindec('11111111')) . $this->generatePayload(65535);
		$raw65536 = chr(bindec('10000001')) . chr(bindec('01111111'));
		$raw65536 .= chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(1) . chr(0) . chr(0);
		$raw65536 .= $this->generatePayload(65536);
		$raw100000 = chr(bindec('10000001')) . chr(bindec('01111111'));
		$raw100000 .= chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(1) . chr(bindec('10000110')) . chr(bindec('10100000'));
		$raw100000 .= $this->generatePayload(100000);
		$in1 = new InboundFrame('somesender', $raw1);
		$in10 = new InboundFrame('somesender', $raw10);
		$in100 = new InboundFrame('somesender', $raw100);
		$in125 = new InboundFrame('somesender', $raw125);
		$in126 = new InboundFrame('somesender', $raw126);
		$in127 = new InboundFrame('somesender', $raw127);
		$in128 = new InboundFrame('somesender', $raw128);
		$in1000 = new InboundFrame('somesender', $raw1000);
		$in10000 = new InboundFrame('somesender', $raw10000);
		$in65534 = new InboundFrame('somesender', $raw65534);
		$in65535 = new InboundFrame('somesender', $raw65535);
		$in65536 = new InboundFrame('somesender', $raw65536);
		$in100000 = new InboundFrame('somesender', $raw100000);

		$this->assertEquals(1, strlen($in1->getPayload()));
		$this->assertEquals(1, $in1->getLength());
		
		$this->assertEquals(10, strlen($in10->getPayload()));
		$this->assertEquals(10, $in10->getLength());

		$this->assertEquals(100, strlen($in100->getPayload()));
		$this->assertEquals(100, $in100->getLength());

		$this->assertEquals(125, strlen($in125->getPayload()));
		$this->assertEquals(125, $in125->getLength());

		$this->assertEquals(126, strlen($in126->getPayload()));
		$this->assertEquals(126, $in126->getLength());

		$this->assertEquals(127, strlen($in127->getPayload()));
		$this->assertEquals(127, $in127->getLength());

		$this->assertEquals(128, strlen($in128->getPayload()));
		$this->assertEquals(128, $in128->getLength());

		$this->assertEquals(1000, strlen($in1000->getPayload()));
		$this->assertEquals(1000, $in1000->getLength());

		$this->assertEquals(10000, strlen($in10000->getPayload()));
		$this->assertEquals(10000, $in10000->getLength());

		$this->assertEquals(65534, strlen($in65534->getPayload()));
		$this->assertEquals(65534, $in65534->getLength());

		$this->assertEquals(65535, strlen($in65535->getPayload()));
		$this->assertEquals(65535, $in65535->getLength());

		$this->assertEquals(65536, strlen($in65536->getPayload()));
		$this->assertEquals(65536, $in65536->getLength());

		$this->assertEquals(100000, strlen($in100000->getPayload()));
		$this->assertEquals(100000, $in100000->getLength());

		// Test validator
		$this->assertEquals(true, $in1->isValid());
		$this->assertEquals(true, $in10->isValid());
	}
}

?>
