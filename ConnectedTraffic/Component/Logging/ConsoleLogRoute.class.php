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
 * This class routes the log-lines onto the STDOUT via simple echo
 * statements.
 */
class ConsoleLogRoute
	extends \ConnectedTraffic\Component\Logging\LogRoute {
	protected $neededConfigs = array('useColors');

	protected $useColors = false;

	/**
	 * This method prints out a red line
	 * @param (string)line the line to log
	 */
	public function logError($line) {
		if ($this->useColors) {
			echo "\e[0;31m" . $line . "\e[0m";
		} else {
			echo $line;
		}
	}

	/**
	 * This method prints out a orange line
	 * @param (string)line the line to log
	 */
	public function logWarning($line) {
		if ($this->useColors) {
			echo "\e[1;31m" . $line . "\e[0m";
		} else {
			echo $line;
		}
	}

	/**
	 * This method prints out a white/normal line
	 * @param (string)line the line to log
	 */
	public function logInfo($line) {
		if ($this->useColors) {
			echo "\e[0;37m" . $line . "\e[0m";
		} else {
			echo $line;
		}
	}

	/**
	 * This method prints out a yellow line
	 * @param (string)line the line to log
	 */
	public function logDebug($line) {
		if ($this->useColors) {
			echo "\e[0;33m" . $line . "\e[0m";
		} else {
			echo $line;
		}
	}
}
