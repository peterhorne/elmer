<?php

namespace Elmer;

class Response {
	
	public $body;
	public $status;
	public $headers;
	
	
	public function __construct($body = '', $status = 200, $headers = array()) {
		$this->body = $body;
		$this->status = $status;
		$this->headers = $headers;
	}
	
	
	public function send() {
		header("Status: {$this->status}", true, $this->status);
		
		foreach ($this->headers as $header => $values) {
			foreach ((array)$values as $value) {
				header("$header: $value");
			}
		}
		
		echo $this->body;
	}
}
