#Elmer

> Weniger, aber besser
> &mdash; <cite>Dieter Rams</cite>

Elmer is a simple and flexible routing library for PHP.

## Getting Started

```php
<?php
require '/path/to/Elmer/Routes.php';
$routes = new Elmer\Routes;

$routes->get('/', function() {
	return 'Hello, world!';
}

$response = $routes->dispatch($request);
$response();
```

...and that's it! We've created a route that responds to the HTTP request `GET /` with `Hello, world!`.

## Methods

A route can respond to different HTTP methods:

```php
<?php
$routes->get();
$routes->post();
$routes->put();
$routes->delete();

// You can use any method you like:
$routes->brew();
```

## Route Parameters

URIs may contain parameters. A parameter starts with a semicolon and is followed by the type of parameter. For example:

```php
<?php
$routes->get('/users/:int', function($id) {
	return "User #: $id";
});
```

This route will match `/users` followed by any digit, eg. `/users/21`.

The types of parameters that are available are:

```php
<?php
:any // Anything (alpha, num, underscore, or dash)
:int // Any integer
:alpha // Any alphabetic character
:alphanum // Any alphanumeric character
:year // 4 digits
:month // 2 digits
:day // 2 digits
```

If you need more control then you can use a custom regex (wrapped in parenthesis to define it as a matched section). The ability to add custom parameter types at runtime is planned for the near future.

### Optional Parameters

Parts of a URI can be marked as optional by appending a question mark like so:

```php
<?php
$routes->get('/articles/:year?', function($year = 2012) { .. });
```

This route will match the request `/articles`, as well as `/articles/2011`.

Don't forget to set a default value for optional parameters!

## Filters

Filters enable you to add additional functionality to your applications, such as logging and authentication.

A basic filter looks like this:

```php
<?php
$routes->filter(function($route)) {
	$response = $route();
	$response['body'] = "Foo{$response['body']}";
	
	return $response;
}

$routes->get('/', function() {
	return 'Bar';
}
```

The filter above prepends 'Foo' to the response so the final result would be `FooBar`. The route is passed to the filter, which is responsible for continuing the execution of the application by calling the route closure. This allows code to be run before/after, modify the response, or skip calling the route altogether.

Filters can be used for a range of tasks, such as authentication or logging, the limit is your imagination!

Filters are inspired by middleware for [Ruby's Rack](http://stackoverflow.com/questions/2256569/what-is-rack-middleware) and [Python's WSGI](http://wsgi.org/)

### Multiple filters

You may use as many filters as you like.

## Groups

Groups enable you to apply a filter to a limited subset of routes. To define a group all you need to do is wrap several routes in the following:

```php
<?php
$routes->group(function($routes) {
	
	// Declare filters here
	// Declare routes here
}
```

Now any filters that are declared within the above group will only be applied to routes declared in that same group.

You may nest groups if you like. Filters apply to sub-groups, but a filter in a sub-group will not be applied to routes in the parent group. For example:

```php
<?php
$routes->group(function($routes) {
	$routes->filter(function($route) { .. }); // Filter A
	$routes->get('/foo', function() { .. });
	
	$routes->group(function($routes) {
		$routes->filter($route) { .. }); // Filter B
		$routes->get('/bar', function() { .. });
	});
});
```

In the above code sample Filter A applies for both routes `/foo` and `/bar`. Filter B only applies to the route `/foo`.

### Prefixing routes

You can apply a prefix to a group of routes by passing in the URI prefix as the first parameter of the group decleration.

```php
<?php
$routes->group('/user', function($routes) {
	$routes->get('/profile', function() { .. });
}
```

The above route will match the request `GET /user/profile`.
