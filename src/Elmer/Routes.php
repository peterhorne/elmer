<?php

namespace Elmer;

class Routes {
	
	private $routes = array();
	private $prefix = '';
	private $decorators = array();
	
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
		
		foreach ($this->decorators as $decorator) {
			// Replace route callback with proxy to the decorator
			$callback = function() use ($decorator, $callback) {
				
				// Get the args that are passed to the route callback
				$args = func_get_args();
				
				// Create the route callback proxy that is passed to the decorator
				// We create a proxy so we don't have to manually pass in any arguments
				$callback = function() use ($callback, $args) {
					return call_user_func_array($callback, $args);
				};
				
				// Add the route callback proxy to the args for the decorator
				array_unshift($args, $callback);
				return call_user_func_array($decorator, $args);
			};
		}
		
		$this->routes[$uri][$method] = $callback;
		
		return $this;
	}
	
	
	public function decorator($decorator) {
		array_unshift($this->decorators, $decorator);
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
			'decorators' => $this->decorators
		);
		$this->prefix .= $prefix;
		$routes($this);
		$this->prefix = $previous['prefix'];
		$this->decorators = $previous['decorators'];
		
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
