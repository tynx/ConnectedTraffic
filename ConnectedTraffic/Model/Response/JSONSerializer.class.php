<?php

namespace ConnectedTraffic\Model\Response;

class JSONSerializer implements SerializerInterface{
	private $rawData = null;
	private $response = null;
	
	public function __construct($response){
		$this->response = $response;
	}

	public function serialize(){
		$assocHeader = array();
		$assocHeader['id'] = $this->response->getHeader()->getId();
		$assocHeader['status'] = $this->response->getHeader()->getStatus();
		$assocHeader['header']['statusMessage'] = $this->response->getHeader()->getStatusMessage();
		$assocHeader['server'] = $this->response->getHeader()->getServer();
		$assocHeader['time'] = $this->response->getHeader()->getTime();
		$assocHeader['length'] = $this->response->getHeader()->getLength();
		$this->rawData = '{"header":' . json_encode($assocHeader) . ', "body":"' . str_replace(array('"', '\/'), array('\"', '/'), $this->response->getBody()) . '"}';
	}

	public function getRawData(){
		return $this->rawData;
	}
}
?>
