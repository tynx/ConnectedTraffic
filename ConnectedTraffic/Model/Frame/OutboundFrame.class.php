<?php

namespace ConnectedTraffic\Model\Frame;

class OutboundFrame extends \ConnectedTraffic\Model\Frame\Frame{
	private $receiver = null;

	public function __construct($receiver, $payload = null){
		$this->receiver = $receiver;
		$this->payload = $payload;
	}

	public function getReceiver(){
		return $this->receiver;
	}

	public function getData(){
		if($this->isHandshake())
			return $this->payload;
		else
			return $this->encapsulate();
	}
}

?>
