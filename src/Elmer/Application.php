<?php

namespace Elmer;

class Application extends DependencyContainer {
	
	private $routes = array();
	private $prefix = '';
	private $filters = array();
	
	public $patterns = array(
		'int' => '(\d+)',
		'any' => '(\w+)'
	);
	
	
	public function add($method, $uri, $callback) {
		$uri = $this->prefix . $uri;
		
		$callback = function() use ($callback) {
			$response = call_user_func_array($callback, func_get_args());
			if (!is_a($response, 'Elmer\Response')); {
				$response = new Response($response);
			}
			return $response;
		};
		
		foreach ($this->filters as $filter) {
			$callback = function() use ($filter, $callback) {
				$args = func_get_args();
				return $filter(function() use ($callback, $args) {
					return call_user_func_array($callback, $args);
				});
			};
		}
		
		$this->routes[$uri][$method] = $callback;
		
		return $this;
	}
	
	
	public function dispatch(Request $request) {
		// Add $request to $app['request']
		$this['request'] = function() use ($request) {
			return $request;
		};
		
		foreach ($this->routes as $uri => $methods) {
			$uri = $this->regexify($uri);
			
			if (preg_match($uri, $request->path, $matches)) {
				array_shift($matches);
				foreach ($methods as $method => $callback) {
					if (strtolower($method) == strtolower($request->method)) {
						return call_user_func_array($callback, $matches);
					}
				}		
				return new Response('Error: 405', 405, array(array('Content-Type', 'text/plain')));
			}
		}
		return new Response('Error: 404', 404, array(array('Content-Type', 'text/plain')));
	}
	
	
	public function filter($filter) {
		array_unshift($this->filters, $filter);
		return $this;
	}
	
	
	public function mount($prefix, $group) {
		
		// Prevents duplicated preceding slashes
		if ($prefix == '/') {
			$prefix = '';
		}
		
		$previous = array(
			'prefix' => $this->prefix,
			'filters' => $this->filters
		);
		$this->prefix .= $prefix;
		$group($this);
		$this->prefix = $previous['prefix'];
		$this->filters = $previous['filters'];
		
		return $this;
	}
	
	
	public function __call($name, $args) {
		array_unshift($args, $name);
		call_user_func_array(array($this, 'add'), $args);
	}
	
	
	private function regexify($uri) {
		$parts = explode('/', $uri);
		foreach ($parts as &$part) {
			// if part begins with ':'
			if (substr($part, 0, 1) == ':') {
				// replace $part with corresponding regex
				$part = $this->patterns[substr($part, 1)];
			}
		}
		
		return '/^' . implode('\/', $parts) . '$/';
	}
}
