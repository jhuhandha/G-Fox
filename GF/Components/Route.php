<?php 
/**
 * This class represents the system, it contains the running application and initializes
 * all the basic logic that every gf-web-application needs.
 *
 * @package GF/Components
 * @author  Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version  1.0.0 
 * @copyright (c) 2017, jakolab
 */
namespace GF\Components;

class Route {
	/**
	 * Route invoked by the browser.
	 * @var string
	 */
	private static $_currentRoute = null;
	/**
	 * Route to be invoked.
	 * @var string
	 */
	private static $_routeToDispatch = null;
	/**
	 * Type of the route to be dispatched (get|post|put|delete).
	 * @var string
	 */
	private static $_routeTDType = null;
	/**
	 * Action to be executed when the route is invoked.
	 * @var mixed
	 */
	private static $_routeAction = null;
	/**
	 * Params passed to the invoked action.
	 * @var array
	 */
	private static $_params = [];

    /**
     * Initializes the component
     */
	public function init(){ }
	/**
	 * Dispatches the route action.
	 * @return mixed
	 */
	public function dispatch(){
		if(is_array(self::$_routeAction)){			
			return $this->invokeClass();
		} else if(is_callable(self::$_routeAction)){
			return [
					'object' => self::$_routeAction, 
					'params' => self::$_params
				];
		}
	}

	private function invokeClass(){
        $object = self::$_routeAction[0];
		$action = isset(self::$_routeAction[1])? self::$_routeAction[1] : 'index';
		unset(self::$_routeAction[0], self::$_routeAction[1]);
		return [
			'object' => $object,
			'action' => $action,
			'params' => self::$_params,
		];
	}
	/**
	 * Find the current route invoked by the browser.
	 */
	public static function findCurrent(){
		$route = filter_input(INPUT_GET, "r", FILTER_SANITIZE_SPECIAL_CHARS);
		self::$_currentRoute = $route == ''? '/' : $route;
		unset($_GET['r']);
	}
	/**
	 * Returns the current route of the application.
	 * @return string
	 */
	public static function getCurrentRoute(){
		return self::$_currentRoute;
	}
	/**
	 * Sets the route to be dispatches and the corresponding parameters.
	 * @param  string $type
	 * @param  string $key
	 * @param  string $uri
	 * @param  mixed $action
	 */
	private static function setRouteToDispatch($type, $key, $uri, $action){
		self::$_routeTDType = $type;
		self::$_routeToDispatch = $key;		
		self::$_routeAction = $action;
	}
	/**
	 * Prepares a get route.
	 * @param  string $uri
	 * @param  mixed $action
	 */
	public static function get($uri, $action) {
		if(self::$_routeToDispatch == null && self::compare($uri)){
			self::setRouteToDispatch("get", $uri, $uri, $action);
		}
	}
	/**
	 * Checks if a route matches with the current route.
	 * @param  string $uri
	 * @return boolean
	 */
	private static function compare($uri){
		if(strpos($uri, '{') === false){ return $uri == self::$_currentRoute; }		
		$pattern = "|^" . preg_replace('/\{(.*?)\}/', '(.*)', $uri) . "|";
		$match = preg_match_all($pattern, self::$_currentRoute, $matches) == 1;
		if($match){
			self::setParams($matches);
		}
		return $match;
	}
	/**
	 * Set the params to the invoked action
	 * @param array $matches
	 */
	private static function setParams($matches){
		if(count($matches) <= 1){ return; }
		unset($matches[0]);
		self::$_params = array_map(function($v){ return $v[0]; }, $matches);
	}
} 