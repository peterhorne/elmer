<?php

namespace Elmer;

class Routes {
	
	private $routes = array();
	private $prefix = '';
	private $filters = array();
	
	public $patterns = array(
		':int' => '(\d+)',
		':any' => '(\w+)'
	);
	
	
	public function __construct($routes) {
		$routes($this);
	}
	
	
	/**
	 * Proxy to $this->add();
	 * Enables $this->get(); or $this->post(); shorthand syntax
	 */
	public function __call($name, $args) {
		array_unshift($args, $name);
		call_user_func_array(array($this, 'add'), $args);
	}
	
	
	public function add($method, $uri, $callback) {
		$uri = $this->prefix . $uri;
		
		foreach ($this->filters as $filter) {
			// Replace route callback with proxy to the filter
			$callback = function() use ($filter, $callback) {
				
				// Get the args that are passed to the route callback
				$args = func_get_args();
				
				// Create the route callback proxy that is passed to the filter
				// We create a proxy so we don't have to manually pass in any arguments
				$callback = function() use ($callback, $args) {
					return call_user_func_array($callback, $args);
				};
				
				// Add the route callback proxy to the args for the filter
				array_unshift($args, $callback);
				return call_user_func_array($filter, $args);
			};
		}
		
		$this->routes[$uri][$method] = $callback;
		
		return $this;
	}
	
	
	public function filter($filter) {
		array_unshift($this->filters, $filter);
		return $this;
	}
	
	
	public function mount($prefix, $routes = null) {
		
		// User doesn't have to specify a prefix (defaults to no prefix)
		if (!$routes) {
			$routes = $prefix;
			$prefix = '';
		}
		
		$previous = array(
			'prefix' => $this->prefix,
			'filters' => $this->filters
		);
		$this->prefix .= $prefix;
		$routes($this);
		$this->prefix = $previous['prefix'];
		$this->filters = $previous['filters'];
		
		return $this;
	}
	
	
	public function __invoke($env) {
		$status_code = 404;
		foreach ($this->routes as $uri => $methods) {
			foreach ($this->patterns as $pattern => $replacement) {
				$uri = str_replace($pattern, $replacement, $uri);
			}
			$uri = '/^' . preg_replace('/\//', '\/', $uri) . '$/';
			
			if (preg_match($uri, $env['path_info'], $matches)) {
				foreach ($methods as $method => $callback) {
					if (strtolower($method) == strtolower($env['request_method'])) {
						$matches[0] = $env; // Replace the matched regex with the environment
						return call_user_func_array($callback, $matches);
					}
				}
				
				$status_code = 405;
			}
		}
		
		return new Response($status_code);
	}
}
