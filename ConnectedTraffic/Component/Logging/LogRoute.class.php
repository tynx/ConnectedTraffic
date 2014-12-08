<?php

namespace ConnectedTraffic\Component\Logging;

abstract class LogRoute{

	protected $levels = array('error', 'warning', 'info', 'debug');
	protected $dateFormat = 'd-m-Y H:i:s';
	protected $neededConfigs = array();

	protected function formatLogLine($line, $category, $level){
		$finalLine = '[' . date($this->dateFormat) . '](';
		$finalLine .= strtoupper($level) . ') ' . $category . ': ';
		return $finalLine . $line . "\n";
	}

	public abstract function logError($line);
	
	public abstract function logWarning($line);
	
	public abstract function logInfo($line);
	
	public abstract function logDebug($line);
	
	public final function log($line, $category, $level){
		if($level == 'error')
			$this->logError($this->formatLogLine($line, $category, $level));
		if($level == 'warning')
			$this->logWarning($this->formatLogLine($line, $category, $level));
		if($level == 'info')
			$this->logInfo($this->formatLogLine($line, $category, $level));
		if($level == 'debug')
			$this->logDebug($this->formatLogLine($line, $category, $level));
	}
	
	public final function setConfig($config){
		foreach($this->neededConfigs as $neededConfig){
			if(!isset($config[$neededConfig]))
				return false;
			$this->$neededConfig = $config[$neededConfig];
		}
		if(isset($config['levels']) && is_array($config['levels']))
			$this->levels = $config['levels'];
		if(isset($config['dateFormat']))
			$this->dateFormat = $config['dateFormat'];
	}

}

?>
