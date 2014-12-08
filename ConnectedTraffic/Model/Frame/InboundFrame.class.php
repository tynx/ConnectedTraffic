<?php

namespace ConnectedTraffic\Model\Frame;

class InboundFrame extends \ConnectedTraffic\Model\Frame\Frame{
	private $sender = null;
	private $unparsedPayload = null;

	public function __construct($sender, $rawData){
		$this->sender = $sender;
		$this->unparsedPayload = $rawData;
		$this->parse($rawData);
	}

	public function getSender(){
		return $this->sender;
	}

	public function getUnparsedPayload(){
		return $this->unparsedPayload;
	}

}

?>
