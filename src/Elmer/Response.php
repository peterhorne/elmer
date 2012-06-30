<?php

namespace Elmer;
use ArrayObject;

class Response extends ArrayObject {
	
	public $status = 200;
	public $body = '';
	public $headers = array('X-Powered-By' => 'Elmer');
	
	
	public function __construct() {
		$args = func_get_args();
		
		// Allow calling with an array
		if (func_num_args() === 1 && is_array($args[0])) {
			$args = $args[0];
		}
		
		call_user_func_array(array($this, 'populate'), $args);
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
	
	
	public static function convertReturnValueToResponse($callback) {
		return function() use ($callback) {
			$response = call_user_func_array($callback, func_get_args());
			if (!is_a($response, 'Elmer\Response')); {
				$response = new Response($response);
			}
			return $response;
		};
	}
	
	
	public function offsetGet($index) {
		if ($index === 0 || $index === 'status') {
			return $this->status;
		}
		
		if ($index === 1 || $index === 'body') {
			return $this->body;
		}
		
		if ($index === 2 || $index === 'headers') {
			return $this->headers;
		}
	}
	
	
	private function populate() {
		foreach (func_get_args() as $arg) {
			switch(gettype($arg)) {
				case 'integer':
					$this->status = $arg;
					break;
				case 'string':
					$this->body = $arg;
					break;
				case 'array':
					$this->headers = array_merge($this->headers, $arg);
					break;
			}
		}
	}
}
