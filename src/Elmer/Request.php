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
		$this->scheme = (empty($_SERVER['HTTPS']) ? 'http' : 'https');
		$this->host = $_SERVER['SERVER_NAME'];
		$this->script = dirname($_SERVER['SCRIPT_NAME']);
		$this->path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '/');
		$this->params = array_merge($_GET, $_POST);
	}
	
	
	public function base() {
		return $this->scheme . '://' . $this->host . $this->script;
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
