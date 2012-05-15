<?php

namespace Elmer;

class DependencyContainer extends \ArrayObject {
	
	protected $dependencies;
	
	public function __construct($config = array()) {
		$this->dependencies['config'] = $config;
	}
	
	public function offsetGet($key) {
		if ($key == 'config') {
			return $this->dependencies['config'];
		} else {
			$dependency = $this->dependencies[$key];
			return $dependency($this);
		}
	}
	
	
	public function offsetSet($key, $value) {
		$this->dependencies[$key] = $value;
	}
	
	
	public function offsetExists($key) {
		return isset($this->dependencies[$key]);
	}
	
	
	public function offsetUnset($key) {
		unset($this->dependencies[$key]);
	}
}
