<?php

namespace Elmer;

class Environment extends DependencyContainer {
	
	public function __construct($vars) {
		foreach ($vars as $key => $val) {
			$this[$key] = $val;
		}
		
		$this['request_root'] = array($this, 'request_root');
		$this['request_url'] = array($this, 'request_url');
	}
	
	
	public static function initFromGlobals() {
		$input = array_merge($_GET, $_POST);
		$cookies = $_COOKIE;
		
		$script_name = dirname($_SERVER['SCRIPT_NAME']);
		$parts = parse_url($_SERVER['REQUEST_URI']);
		$path_info = str_replace($script_name, '', $parts['path']); // $_SERVER['PATH_INFO'] ignores duplicate slashes
		
		return new self(array(
			'request_method' => isset($input['_method']) ? $input['_method'] : $_SERVER['REQUEST_METHOD'],
			'script_name' => $script_name,
			'path_info' => $path_info,
			'query_string' => $_SERVER['QUERY_STRING'],
			'server_name' => $_SERVER['SERVER_NAME'],
			'server_port' => $_SERVER['SERVER_PORT'],
			'server_scheme' => empty($_SERVER['HTTPS']) ? 'http' : 'https',
			'input' => $input,
			'cookies' => $cookies,
		));
	}
	
	
	public function request_root($env) {
		$root = '';
		$root .= $env['server_scheme'] . '://';
		$root .= $env['server_name'];
		$root .= ($env['server_port'] == 80) ? '' : ":{$env['server_port']}";
		$root .= $env['script_name'];
		return $root;
	}
	
	
	public function request_url($env) {
		$query_string = $env['query_string'] ? "?{$env['query_string']}" : '';
		return $env['request_root'] . $env['path_info'] . $query_string;
	}
}
