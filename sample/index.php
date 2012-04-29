<?php

use Elmer\Routes;
use Elmer\Request;
use Elmer\Response;

require '../vendor/autoload.php';
$routes = new Routes;
$request = new Request;

$routes->group(function($routes) use ($request) {
	
	$routes->filter(function($route) {
		if (false) {
			return new Response('Route not called.');
		}
		
		return $route();
	});
	
	$routes->get('/', function() use ($request) {
		return new Response('Hello, world');
	});
});

$routes->dispatch($request)->send();
