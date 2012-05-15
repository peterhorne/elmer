<?php

use Elmer\DependencyContainer;

class DependencyContainerTest extends PHPUnit_Framework_TestCase {
	
	public function testConfig() {
		$container = new DependencyContainer(
			array('author' => 'Peter Horne')
		);
		
		$this->assertEquals('Peter Horne', $container['config']['author']);
	}
	
	
	public function testGetService() {
		$container = new DependencyContainer;
		$container['foo'] = function() {
			return 'bar';
		};
		
		$this->assertEquals('bar', $container['foo']);
	}
	
	
	public function testServiceCanAccessContainer() {
		$container = new DependencyContainer;
		$container['foo'] = function() {
			return 'bar';
		};
		$container['baz'] = function($container) {
			return $container['foo'];
		};
		
		$this->assertEquals('bar', $container['baz']);
	}
}
