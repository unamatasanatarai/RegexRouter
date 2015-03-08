<?php
/**
 * @version 1.0
 */

namespace RegexRouter;
use Exception;

class ReverseRegexRouter
{
	/**
	 * Must NOT end in slash. for example: http://example.com/folder
	 */
	public $full_base_url = '';

	/**
	 * Cached settings
	 */
	public $routes = array();

	public function compile($name, $params){
		if (!isset($this->routes[$name])){
			throw new Exception('Route not found');
		}
		$route       = $this->routes[$name];
		$path        = $route['path'];
		$params_copy = $params;

		foreach($params as $named => $value){
			if (isset($route['named'][$named])){
				$path = preg_replace('%\{' . $named . '(\:[a-zA-Z]*)?\}%', $value, $path);
				unset($params_copy[$named]);
			}
		}

		$get = array();
		foreach($params_copy as $name => $param){
			$get[] = $name . '=' . $param;
		}

		if (!empty($get)){
			$path .= '?' . implode('&', $get);
		}

		return $this->full_base_url . '/' . $this->purgeUnusedTags($path);
	}

	public function purgeUnusedTags($path){
		return preg_replace('%\{[a-zA-Z]+(\:[a-zA-Z]*)?\}%', '', $path);
	}
}