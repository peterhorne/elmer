<?php

use Elmer\Response;

class ResponseTest extends PHPUnit_Framework_TestCase {
	
	public function testArrayAccess() {
		$status = 200;
		$body = 'Hello, world';
		$headers = array('X-Foo' => 'bar');
		$response = new Response($status, $body, $headers);
		
		$this->assertEquals($status, $response['status']);
		$this->assertEquals($body, $response['body']);
		$this->assertEquals($headers['X-Foo'], $response['headers']['X-Foo']);
	}
	
	
	public function testInitialiseWithNoParameters() {
		$response = new Response;
		
		$this->assertInternalType('integer', $response['status']);
		$this->assertInternalType('string', $response['body']);
		$this->assertInternalType('array', $response['headers']);
	}
	
	
	public function testInitialiseWithInt() {
		$status = 404;
		$response = new Response($status);
		
		$this->assertEquals($status, $response['status']);
	}
	
	
	public function testInitialiseWithString() {
		$body = 'Hello, world';
		$response = new Response($body);
		
		$this->assertEquals($body, $response['body']);
	}
	
	
	public function testInitialiseWithArray() {
		$status = 200;
		$body = 'Hello, world';
		$headers = array('X-Foo' => 'bar');
		$response = new Response(array($body, $status, $headers));
		
		$this->assertEquals($body, $response['body']);
		$this->assertEquals($status, $response['status']);
		$this->assertEquals($headers['X-Foo'], $response['headers']['X-Foo']);
	}
	
	
	public function testInitialiseWithStringAndInt() {
		$body = 'Hello, world';
		$status = 200;
		$response = new Response($status, $body);
		
		$this->assertEquals($status, $response['status']);
		$this->assertEquals($body, $response['body']);
	}
}
