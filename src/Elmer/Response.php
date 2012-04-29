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
		header(true, true, $this->status);
		
		foreach ($this->headers as $header) {
			header("{$header[0]}: {$header[1]}");
		}
		
		echo $this->body;
	}
}
