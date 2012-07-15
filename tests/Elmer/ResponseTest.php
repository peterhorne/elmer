<?php

use Elmer\Response;

class ResponseTest extends PHPUnit_Framework_TestCase {
	
	public function testGetByKey() {
		$status = 200;
		$body = 'Hello, world';
		$headers = array('X-Foo' => 'bar');
		$response = new Response($status, $body, $headers);
		
		$this->assertEquals($status, $response['status']);
		$this->assertEquals($body, $response['body']);
		$this->assertEquals($headers['X-Foo'], $response['headers']['X-Foo']);
	}
	
	
	public function testGetByIndex() {
		$status = 200;
		$body = 'Hello, world';
		$headers = array('X-Foo' => 'bar');
		$response = new Response($status, $body, $headers);
		
		$this->assertEquals($status, $response[0]);
		$this->assertEquals($body, $response[1]);
		$this->assertEquals($headers['X-Foo'], $response[2]['X-Foo']);
	}
	
	
	public function testSetByKey() {
		$status = 200;
		$body = 'Hello, world';
		$headers = array('X-Foo' => 'bar');
		$response = new Response;
		$response['status'] = $status;
		$response['body'] = $body;
		$response['headers'] = $headers;
		
		$this->assertEquals($status, $response['status']);
		$this->assertEquals($body, $response['body']);
		$this->assertEquals($headers['X-Foo'], $response['headers']['X-Foo']);
	}
}
