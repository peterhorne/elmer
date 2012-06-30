<?php

namespace Elmer;

class Environment extends DependencyContainer {
	
	public function __construct($request_method, $script_name, $path_info, $server_name, $server_port, $input) {
		$this['REQUEST_METHOD'] = $request_method;
		$this['SCRIPT_NAME'] = $script_name;
		$this['PATH_INFO'] = $path_info;
		$this['SERVER_NAME'] = $server_name;
		$this['SERVER_PORT'] = $server_port;
		$this['input'] = $input;
		
		$this['BASE'] = array($this, 'base')
	}
	
	public static function initFromGlobals() {
		
		$this->params = array_merge($_GET, $_POST);
		$this->method = $this['_method'] ?: $_SERVER['REQUEST_METHOD'];
		$this->scheme = empty($_SERVER['HTTPS']) ? 'http' : 'https';
		$this->host = $_SERVER['SERVER_NAME'];
		$this->script = dirname($_SERVER['SCRIPT_NAME']);
		
		$filename = basename($_SERVER['SCRIPT_FILENAME']);
		$prefix = str_replace($filename, '', $_SERVER['SCRIPT_NAME']);
		$parts = parse_url($_SERVER['REQUEST_URI']);
		$this->path = '/' . str_replace($prefix, '', $parts['path']);
	}
	
	
	public function base() {
		return $this->scheme . '://' . $this->host . $this->script;
	}
	
	
	public function url() {
		return $this->base() . $this->path;
	}
	
	
	public function offsetGet($offset) {
		return isset($this->params[$offset]) ? $this->params[$offset] : null;
	}
	
	
	public function offsetSet($offset, $value) {
		if ($offset === null) {
			$this->params[] = $value;
		} else {
			$this->params[$offset] = $value;
		}
	}
	
	
	public function offsetExists($offset) {
		return isset($this->params[$offset]);
	}
	
	
	public function offsetUnset($offset) {
		unset($this->params[$offset]);
	}
}
