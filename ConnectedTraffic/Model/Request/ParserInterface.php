<?php

namespace ConnectedTraffic\Model\Request;

Interface ParserInterface {
	public function __construct($rawData);
	public function parse();
	public function getHeader();
	public function getBody();
	public function getErrorMessage();
}
