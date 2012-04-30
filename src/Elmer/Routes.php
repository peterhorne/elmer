<?php

namespace Elmer;

class Routes {
	
	private $routes = array();
	private $prefix = '';
	private $filters = array();
	
	public $patterns = array(
		'int' => '(\d+)',
		'any' => '(\w+)'
	);
	
	
	public function add($method, $uri, $callback) {
		$uri = $this->prefix . $uri;
		
		foreach ($this->filters as $filter) {
			$callback = function() use ($filter, $callback) {
				$args = func_get_args();
				return $filter(function() use ($callback, $args) {
					return call_user_func_array($callback, $args);
				});
			};
		}
		
		$this->routes[$uri][$method] = $callback;
	}
	
	
	public function dispatch(Request $request) {
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
		$this->filters[] = $filter;
	}
	
	
	public function group($prefix, $group = null) {
		
		// $prefix is optional
		if ($group == null) {
			$group = $prefix;
			$prefix = '';
		}
		
		$previous = $this->prefix;
		$this->prefix .= $prefix;
		$group($this);
		$this->prefix = $previous;
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
