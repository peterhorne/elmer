<?php

use Elmer\Application;
use Elmer\Request;
use Elmer\Response;

require '../vendor/autoload.php';
$app = new Application;

$app->mount('/', function($app) {
	
	$app->filter(function($route) {
		if (true) {
			return $route();
		} else {
			return 'Route not called.';
		}
	});
	
	$app->get('/', function() {
		return 'Hello, world';
	});
});

$app->dispatch(new Request)->send();
