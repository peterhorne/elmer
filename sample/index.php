<?php

use Elmer\Routes;
use Elmer\Request;
use Elmer\Response;

require '../vendor/autoload.php';
$routes = new Routes;

$routes->group(function($routes) {
	
	$routes->filter(function($route) {
		if (false) {
			return new Response('Route not called.');
		}
		
		return $route();
	});
	
	$routes->get('/', function() {
		return new Response('Hello, world');
	});
});

$routes->dispatch(new Request)->send();
