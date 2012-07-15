<?php

namespace Elmer;
use ArrayObject;

class Response extends ArrayObject {
	
	public $status = 200;
	public $body = '';
	public $headers = array('X-Powered-By' => 'Elmer');
	
	
	public function __construct($status = 200, $body = '', $headers = array()) {
		$this->status = $status;
		$this->body = $body;
		$this->headers = array_merge($this->headers, $headers);
	}
	
	
	public function send() {
		header("Status: {$this->status}", true, $this->status);
		
		foreach ($this->headers as $header => $values) {
			foreach ((array)$values as $value) {
				header("$header: $value", false);
			}
		}
		
		echo $this->body;
	}
	
	
	public function offsetGet($key) {
		if ($key === 0 || $key === 'status') {
			return $this->status;
		}
		
		if ($key === 1 || $key === 'body') {
			return $this->body;
		}
		
		if ($key === 2 || $key === 'headers') {
			return $this->headers;
		}
	}
	
	
	public function offsetSet($key, $value) {
		if ($key === 0 || $key === 'status') {
			return $this->status = $value;
		}
		
		if ($key === 1 || $key === 'body') {
			return $this->body = $value;
		}
		
		if ($key === 2 || $key === 'headers') {
			return $this->headers = $value;
		}
	}
}
