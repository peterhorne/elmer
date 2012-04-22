#Elmer

> Weniger, aber besser
> &mdash; <cite>Dieter Rams</cite>

Elmer is a simple and flexible routing library for PHP. A route matches a URI and a method to a callback.

## Getting Started

```php
<?php
require '/path/to/Elmer/Routes.php';

$routes = new Elmer\Routes;
$request = new Elmer\Request;
$response = new Elmer\Response;

$routes->get('/', function() use ($response) {
	return $response['body'] = 'Hello, world!';
}

$response = $routes->dispatch($request);
$response();
```

...and that's it! We've initialised the routes registry, added a route that responds to the HTTP request `GET /`, then called it and displayed the response: `Hello, world!`.

## Methods

You can respond to different HTTP methods like this:

```php
<?php
$routes->get();
$routes->post();
$routes->put();
$routes->delete();
```

You can use any method name you like. The following is valid:

```php
<?php
$routes->brew();
```

## Route Parameters

URIs may contain parameters. A parameter starts with a semicolon and is followed by the type of parameter. For example:

```php
<?php
$routes->get('/users/:int', ..);
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
$routes->filter(function($env, $route)) {
	// code that runs before
	$response = $route();
	// code that runs after
	
	return $response;
}

$routes->get('/', function($env) {
	return $env['response']->content('The filter above is responsible for calling me.');
}
```

As you can see, the filter is responsible for continuing the execution of the applications by calling the route closure. This allows us to run code before/after, modify the response, or skip calling the route altogether. We could restrict access to a route with the following filter:

```php
<?php
$routes->filter(funciton($env, $route)) {
	if($env['user']->isAdmin()) {
		return $code();
	} else {
		return $env['response']->content('You are not authorised to view this page.');
	}
}
```

We check if the user is an admin and, if they are, we continue execution as usual. If they are not an admin then we respond with an error.

Filters are somewhat similar to middleware for [Ruby's Rack](http://stackoverflow.com/questions/2256569/what-is-rack-middleware) and [Python's WSGI](http://wsgi.org/)

### Multiple filters

You can apply multiple filters to a route. The filters are called in the order that they are defined. In order to maintain proper seperation of concerns filters have no knowledge of other filters' existance.

## Groups

Often we want to apply a filter to a limited subset of routes; groups enable us to do so.

To define a group all you need to do is wrap several routes in the following:

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
	$routes->filter(function($env, $route) { .. }); // Filter A
	$routes->get('/foo', function($env) { .. });
	
	$routes->group(function($routes) {
		$routes->filter($env, $route) { .. }); // Filter B
		$routes->get('/bar', function($env) { .. });
	});
});
```

In the above code sample Filter A applies for both routes `/foo` and `/bar`. Filter B only applies to the route `/foo`.

### Prefixing routes

You can apply a prefix to a group of routes by passing in the URI prefix as the first parameter of the group decleration.

```php
<?php
$routes->group('/user', function($routes) {
	// Declare routes here
}
```
