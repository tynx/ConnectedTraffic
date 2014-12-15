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

namespace ConnectedTraffic\Component;

class Config {

	private $config = array(
		'server'=>array(
			'bindAddress'=>'0.0.0.0',
			'bindPort'=>'9876',
			'pingTimeout'=>30,
			'protocolFormat'=>'JSON',
		),
		'components'=>array(),
		'app'=>array(),
		'params'=>array(),
	);

	public function __construct($config){
		if(isset($config['server']) && is_array($config['server'])) {
			$this->config['server'] = $config['server'];
		}
		if(isset($config['components']) && is_array($config['components'])){
			foreach($config['components'] as $component => $cconfig) {
				$this->config['components'][$component] = $cconfig;
			}
		}
		if(isset($config['app']) && is_array($config['app'])) {
			$this->config['app'] = $config['app'];
		}
		if(isset($config['params']) && is_array($config['params'])) {
			$this->config['params'] = $config['params'];
		}
	}

	public function getServerConfig($property = null){
		if ($property === null) {
			return $this->config['server'];
		} elseif (isset($this->config['server'][$property])) {
			return $this->config['server'][$property];
		}
		return null;
	}

	public function getComponentConfig($component){
		if(isset($this->config['components'][$component]) &&
			is_array($this->config['components'][$component])) {
			return $this->config['components'][$component];
		}
	}

	public function getAppConfig($property = null){
		if ($property === null) {
			return $this->config['app'];
		} elseif (isset($this->config['app'][$property])) {
			return $this->config['app'][$property];
		}
		return null;
	}

	public function getParam($property = null){
		if ($property === null) {
			return $this->config['params'];
		} elseif (isset($this->config['params'][$property])) {
			return $this->config['params'][$property];
		}
		return null;
	}
}
