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

/**
 * This class routes the log-lines into a specific file.
 * TODO: implement!
 */
class FileLogRoute
	extends \ConnectedTraffic\Component\Logging\LogRoute {
	protected $neededConfigs = array('file');

	protected $file = null;
	protected $fh = null;

	public function __destruct(){
		fclose($this->fh);
	}

	private function checkFile(){
		if($this->fh !== null){
			return true;
		}
		$this->fh = fopen(APP_ROOT . '/' . $this->file, 'a');
		if($this->fh === null)
			return false;
		return true;
	}

	private function writeToFile($data){
		if(!$this->checkFile())
			return false;
		for ($written = 0; $written < strlen($data); $written += $fwrite) {
			$fwrite = fwrite($this->fh, substr($data, $written));
			if ($fwrite === false) {
				return $written;
			}
		}
		return $written;
	}

	public function logError($line) {
		$this->writeToFile($line);
	}
	
	public function logWarning($line) {
		$this->writeToFile($line);
	}
	
	public function logInfo($line) {
		$this->writeToFile($line);
	}
	
	public function logDebug($line) {
		$this->writeToFile($line);
	}
}
