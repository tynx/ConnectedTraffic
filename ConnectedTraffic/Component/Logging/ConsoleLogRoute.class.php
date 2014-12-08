<?php

namespace ConnectedTraffic\Component\Logging;

class ConsoleLogRoute extends \ConnectedTraffic\Component\Logging\LogRoute{
	public function logError($line){
		echo $line;
	}
	
	public function logWarning($line){
		echo $line;
	}
	
	public function logInfo($line){
		echo $line;
	}
	
	public function logDebug($line){
		echo $line;
	}
}

?>
