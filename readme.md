#Elmer

> Weniger, aber besser
> &mdash; <cite>Dieter Rams</cite>

Elmer is a simple and flexible web routing framework for PHP.

## Getting Started

```php
<?php

use Elmer\Application;
use Elmer\Request;
use Elmer\Response;

require '../vendor/autoload.php';
$app = new Application;

$app->get('/', function() {
	return 'Hello, world';
});

$response = $app->dispatch(new Request);
$response->send();
```

This is all it takes to create an application that says 'Hello, world' when a user navigates their browser to the root of your site.

## Methods

We assigned a new route to our application by calling the `get` method on our application instance. The method refers to the HTTP method and we can respond to any HTTP method by changing the method that we call on our application:

```php
<?php
$app->post();
$app->delete();
$app->brew(); // Custom methods are supported
```

## Route Parameters

URIs may contain parameters. A parameter starts with a semicolon and is followed by the type of parameter:

```php
<?php
$app->get('/users/:int', function($app, $id) {
	return "You are user #$id";
});
```

This route will match `/users` followed by any digit, eg. `/users/21`.

You may use the following parameters:

```php
:any // Anything (alpha, num, underscore, or dash)
:int // Any integer
:alpha // Any alphabetic character
:alphanum // Any alphanumeric character
:year // 4 digits
:month // 1 - 12
:day // 1 - 31
```

You can add custom parameters by modifying the `patterns` property of your application instance like so:

```php
<?php
$app->patterns['names'] = '(peter|simon|john)';
```

You may also write your own custom regex inline. Be sure to wrap it in parenthesis so it is counted as a matched section.

### Optional Parameters

Parts of a URI can be marked as optional by appending a question mark:

```php
<?php
$app->get('/articles/:year?', function($app, $year = 2012) { .. });
```

This route will match the request `/articles`, as well as `/articles/2011`.

Don't forget to set a default value for optional parameters!

## Responses

TODO

## Filters

Filters 'wrap' around routes so that you can add additional functionality to your application, such as logging and authentication.

A filter looks like this:

```php
<?php
$app->filter(function($app, $route)) {
	$response = $route();
	$response['body'] .= 'Bar';
	
	return $response;
}

$app->get('/', function() {
	return 'Foo';
}
```

The filter above appends 'Bar' to the response so the final result would be `FooBar`. The route is passed to the filter, which is responsible for continuing the execution of the application by calling the route closure. This allows code to be run before/after, modify the response, or skip calling the route altogether.

Filters can be used for a range of tasks, such as authentication or logging, the limit is your imagination!

Filters are inspired by middleware for [Ruby's Rack](http://stackoverflow.com/questions/2256569/what-is-rack-middleware) and [Python's WSGI](http://wsgi.org/)

### Multiple filters

You may use as many filters as you like.

## Groups

Groups enable you to apply a filter to a limited subset of routes. To define a group all you need to do is wrap several routes in the following:

```php
<?php
$app->group(function($app) {
	
	// Declare filters here
	// Declare routes here
}
```

Now any filters that are declared within the above group will only be applied to routes declared in that same group.

You may nest groups if you like. Filters apply to sub-groups, but a filter in a sub-group will not be applied to routes in the parent group. For example:

```php
<?php
$app->group(function($app) {
	$app->filter(function($route) { .. }); // Filter A
	$app->get('/foo', function() { .. });
	
	$app->group(function($app) {
		$app->filter($route) { .. }); // Filter B
		$app->get('/bar', function() { .. });
	});
});
```

In the above code sample Filter A applies for both routes `/foo` and `/bar`. Filter B only applies to the route `/foo`.

### Prefixing routes

You can apply a prefix to a group of routes by passing in the URI prefix as the first parameter of the group decleration.

```php
<?php
$app->group('/user', function($app) {
	$app->get('/profile', function() { .. });
}
```

The above route will match the request `GET /user/profile`.
