<?php

namespace ConnectedTraffic\Model\Response;

Interface SerializerInterface {
	public function __construct($response);
	public function serialize();
	public function getRawData();
}
