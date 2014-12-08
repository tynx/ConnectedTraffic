<?php

namespace ConnectedTraffic\Model\Response;

class Response{
	private $receiver = null;
	private $header = null;
	private $body = null;
	private $serializer = null;
	
	public function __construct($receiver, $body=null, $status = 0, $statusMessage = 'OK'){
		$this->receiver = $receiver;
		$this->header = new ResponseHeader(sha1(uniqid() . time()), strlen($body), $status, $statusMessage);
		$this->body = $body;
		//conf based
		$this->serializer = new JSONSerializer($this);
		$this->serializer->serialize();
	}
	
	public function getHeader(){
		return $this->header;
	}
	
	public function getBody(){
		return $this->body;
	}
	
	public function getReceiver(){
		return $this->receiver;
	}
	
	public function getRawData(){
		return $this->serializer->getRawData();
	}
}
?>
