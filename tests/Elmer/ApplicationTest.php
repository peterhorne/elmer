<?php

use Elmer\Application;
use Elmer\Request;

class ApplicationTest extends PHPUnit_Framework_TestCase {
	
	public function testAddRoute() {
		$app = new Application;
		
		$callback = $this->getMock('stdClass', array('__invoke'));
		$callback->expects($this->once())->method('__invoke');
		
		$app->add('get', '/', $callback);
		
		$request = new Request(array(
			'method' => 'get',
			'path' => '/',
			'scheme' => 'http',
			'host' => 'localhost',
			'script' => '/',
			'params' => array(),
		));
		
		$app->dispatch($request);
	}
	
	
	public function testAddRouteShorthandSyntax() {
		$app = new Application;
		
		$callback = $this->getMock('stdClass', array('__invoke'));
		$callback->expects($this->exactly(5))->method('__invoke');
		
		$app->get('/', $callback);
		$app->put('/', $callback);
		$app->post('/', $callback);
		$app->delete('/', $callback);
		$app->brew('/', $callback);
		
		$request = new Request(array(
			'method' => 'get',
			'path' => '/',
			'scheme' => 'http',
			'host' => 'localhost',
			'script' => '/',
			'params' => array(),
		));
		
		$request->method = 'get';
		$app->dispatch($request);
		$request->method = 'put';
		$app->dispatch($request);
		$request->method = 'post';
		$app->dispatch($request);
		$request->method = 'delete';
		$app->dispatch($request);
		$request->method = 'brew';
		$app->dispatch($request);
	}
	
		
	public function testNoMatchingRouteReturns404() {
		$app = new Application;
		$request = new Request(array(
			'method' => 'get',
			'path' => '/',
			'scheme' => 'http',
			'host' => 'localhost',
			'script' => '/',
			'params' => array(),
		));
		$response = $app->dispatch($request);
		$this->assertEquals(404, $response['status']);
	}
	
	
	public function testIncorrectMethodReturns405() {
		$app = new Application;
		$app->post('/', function() {
			return '';
		});
		$request = new Request(array(
			'method' => 'get',
			'path' => '/',
			'scheme' => 'http',
			'host' => 'localhost',
			'script' => '/',
			'params' => array(),
		));
		$response = $app->dispatch($request);
		$this->assertEquals(405, $response['status']);
	}
	
	
	public function testRouteReturnValueIsConvertedToResponse() {
		$app = new Application;
		$app->get('/', function() {
			return array(200, 'Hello, world');
		});
		$request = new Request(array(
			'method' => 'get',
			'path' => '/',
			'scheme' => 'http',
			'host' => 'localhost',
			'script' => '/',
			'params' => array(),
		));
		$response = $app->dispatch($request);
		$this->assertInstanceOf('Elmer\Response', $response);
	}
}
