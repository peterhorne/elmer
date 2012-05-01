<?php

namespace Elmer;
use ArrayAccess;

class Request implements ArrayAccess {
	public $path;
	public $method;
	public $script;
	public $host;
	public $scheme;
	
	private $params;
	
	public function __construct() {
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->scheme = (!empty($_SERVER['HTTPS']) ? 'https' : 'http');
		$this->host = $_SERVER['SERVER_NAME'];
		$this->path = $this->path();
		$this->script = $this->script($_SERVER['REQUEST_URI'], $this->path);
		$this->params = array_merge($_GET, $_POST);
	}
	
	
	private function path() {
		if (isset($_SERVER['PATH_INFO'])) {
			return $_SERVER['PATH_INFO'];
		}
		
		if (isset($_SERVER['ORIG_PATH_INFO'])) {
			return $_SERVER['ORIG_PATH_INFO'];
		}
		
		return '/';
	}
	
	
	private function script($request, $path) {
		$path = '/'.preg_quote($path, '/').'$/';
		return preg_replace($path, '', $request);
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
