<?php

namespace ConnectedTraffic\Component\Logging;

class FileLogRoute extends \ConnectedTraffic\Component\Logging\LogRoute{
	public function logError($line){
		echo 'to_file'.$line;
	}
	
	public function logWarning($line){
		echo 'to_file'.$line;
	}
	
	public function logInfo($line){
		echo 'to_file'.$line;
	}
	
	public function logDebug($line){
		echo 'to_file'.$line;
	}
}

?>
