<?php

namespace Elmer;

class DependencyContainer extends \ArrayObject {
	
	protected $dependencies;
	
	
	public function __construct($config = array()) {
		$this->dependencies['config'] = $config;
	}
	
	
	public function offsetGet($key) {
		$dependency = $this->dependencies[$key];
		if (is_callable($dependency)) {
			return $dependency($this);
		} else {
			return $dependency;
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
