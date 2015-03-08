<?php
/**
 * @version 1.0
 */

namespace RegexRouter;

class RegexRouter
{
	public $patterns = array(
		'ANY'  => '[\w0-9_\-]+',
		'SLUG' => '[\w0-9_\-]+',
		'ID'   => '[0-9]+',
	);

	/**
	 * Must NOT end in slash. for example: http://example.com/folder
	 */
	public $full_base_url = '';

	/**
	 * Cached settings
	 */
	public $routes = array();

	public function connect($params = array())
	{

		$path           = $this->__cleanPath($params);
		$named          = $this->__extractNamed($path);
		$case_sensitive = isset($params['cs'])
			? !!$params['cs']
			: false;

		$prepared_params = array_merge(
			$params,
			array(
				'type'     => $this->__getType($params),
				'path'     => $path,
				'named'    => $named,
				'prepared' => $this->__prepare($path, $named, $case_sensitive),
			)
		);

		$this->routes[$params['as']] = $prepared_params;
	}

	private function __getType($params){
		if (!isset($params['type'])){
			return array('GET');
		}
		return explode('|', $params['type']);
	}

	private function __cleanPath($params){
		if (!isset($params['path'])){
			return '';
		}
		return trim($params['path'], '/');
	}

	private function __extractNamed($path){
		preg_match_all('/\{([a-z]+(\:[A-Z]+)?)\}/i', $path, $matches);
		if (!is_array($matches[1]) || sizeOf($matches[1]) == 0){
			return array();
		}

		$named_patterns = array();
		foreach($matches[1] as $pair){
			$pair = explode(':', $pair);
			if (sizeOf($pair) == 1){
				$pair[1] = 'ANY';
			}
			$named_patterns[$pair[0]] = '(?P<' . $pair[0] . '>' . $this->patterns[$pair[1]] . ')';
		}
		return $named_patterns;
	}

	private function __prepare($path, $named, $case_sensitive = false){
		$path = preg_replace('/\:[A-Z]+\}/', '}', $path);
		foreach($named as $key => $regex){
			$path = str_replace('{' . $key . '}', $regex, $path);
		}
		return '%^' . $path . '$%' . ($case_sensitive?'':'i') . 'u';
	}

	public function parseRequest($request){
		$req = array();
		$request = trim($request, '/');

		$req['request'] = str_replace($this->full_base_url, '', $request);
		$split          = explode('?', $req['request']);
		$req['url']     = trim($split[0], '/');
		$req['query']   = array();

		if (sizeOf($split) > 1){
			$req['query'] = $this->query2obj($split[1]);
		}

		return $req;
	}

	public function match($request = '')
	{
		$request = $this->parseRequest($request);

		foreach($this->routes as $route)
		{
			if (preg_match($route['prepared'], $request['url'], $matches))
			{
				$params = array();
				foreach($route['named'] as $key => $regex){
					$params[$key] = $matches[$key];
				}
				$route['params'] = $params;
				return $route;
			}
		}

		return false;
	}

	public function query2obj($query_str)
	{
		$req = explode('&', $query_str);
		$ret = array();
		foreach($req as $twins){
			$twins     = explode('=', $twins);
			$key       = array_shift($twins);
			$ret[$key] = is_array($twins)?implode('=', $twins):null;
		}
		return $ret;
	}
}