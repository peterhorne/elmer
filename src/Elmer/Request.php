<?php

namespace Elmer;

class Request {
	public $path;
	public $method;
	public $script;
	public $host;
	public $scheme;
	
	public function __construct() {
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->scheme = (!empty($_SERVER['HTTPS']) ? 'https' : 'http');
		$this->host = $_SERVER['SERVER_NAME'];
		$this->path = $this->path();
		$this->script = $this->script($_SERVER['REQUEST_URI'], $this->path);
	}
	
	
	private function path() {
		$regex = '/^' . preg_quote($_SERVER['SCRIPT_NAME'], '/') . '/';
		return preg_replace($regex, '', $_SERVER['PHP_SELF']);
	}
	
	
	private function script($request, $path) {
		$path = '/'.preg_quote($path, '/').'$/';
		return preg_replace($path, '', $request);
	}
}
