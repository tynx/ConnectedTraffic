<?php
/*
 * This file is part of ConnectedTraffic.
 *
 * ConnectedTraffic is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or any later version.

 * ConnectedTraffic is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with ConnectedTraffic.  If not, see
 * <http://www.gnu.org/licenses/>.
 */

namespace ConnectedTraffic\Component\Logging;

abstract class LogRoute {

	protected $levels = array('error', 'warning', 'info', 'debug');
	protected $dateFormat = 'd-m-Y H:i:s';
	protected $neededConfigs = array();

	protected function formatLogLine($line, $category, $level) {
		$finalLine = '[' . date($this->dateFormat) . '](';
		$finalLine .= strtoupper($level) . ') ' . $category . ': ';
		return $finalLine . $line . "\n";
	}

	public abstract function logError($line);
	
	public abstract function logWarning($line);
	
	public abstract function logInfo($line);
	
	public abstract function logDebug($line);
	
	public final function log($line, $category, $level) {
		if ($level === 'error') {
			$this->logError($this->formatLogLine($line, $category, $level));
		} elseif ($level === 'warning') {
			$this->logWarning($this->formatLogLine($line, $category, $level));
		} elseif ($level === 'info') {
			$this->logInfo($this->formatLogLine($line, $category, $level));
		} elseif ($level === 'debug') {
			$this->logDebug($this->formatLogLine($line, $category, $level));
		}
	}
	
	public final function setConfig($config) {
		foreach ($this->neededConfigs as $neededConfig) {
			if (!isset($config[$neededConfig])) {
				return false;
			}
			$this->$neededConfig = $config[$neededConfig];
		}
		if (isset($config['levels']) && is_array($config['levels'])) {
			$this->levels = $config['levels'];
		}
		if (isset($config['dateFormat'])) {
			$this->dateFormat = $config['dateFormat'];
		}
	}
}
