<?php 
/**
 * This class represents the running application in the system
 * @package GF
 * @author  Jorge Alejandro Quiroz Serna <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2017 jakolab
 */
namespace GF;

use Exception;
use GF\Components\Route AS Route;

final class WebApplication {
    /**
     * Router component
     * @var \GF\Components\Route
     */
	private $router;
    /**
     * Response component
     * @var \GF\Components\Response
     */
	private $response;
    /**
     * Request component
     * @var \GF\Components\Request
     */
	private $request;

    /**
     * Initialize the application
     */
	public function init(){
		$this->initAutoload();
		$this->loadRoutes();
		$this->loadComponents();
		$this->initComponents();
	}

    /**
     * Starts the application and executes all the components behavior
     */
	public function start(){
		$this->request->run();
		$this->response->send();
	}

    /**
     * Initializes the router component, which read all the routes
     * defined in the config.routes file
     */
	private function loadRoutes(){
		Route::findCurrent();
		System::import("Config.routes", true);
	}

    /**
     * Instance all the application components
     */
	private function loadComponents(){
		$this->router = new \GF\Components\Route();
		$this->response = new \GF\Components\Response();
		$this->request = new \GF\Components\Request($this->router->dispatch(), $this->response);
	}

    /**
     * Call the initialization of all components
     */
	private function initComponents(){
		$this->router->init();
	}

    /**
     * Starts the autoload behavior
     */
	private function initAutoload(){
		spl_autoload_register([$this, "loadClass"]);
	}

    /**
     * This function adds the autoload functionality
     * @param string $_class
     * @throws Exception
     */
	private function loadClass($_class){
		$route = $this->fromNSToDots($_class);
		if(!System::fileExists($route)){
			throw new Exception("The Class '$_class' does not exists.");
		}
		System::import($route);
	}

    /**
     * @param string $namespace
     * @return bool|mixed
     */
	private function fromNSToDots(string $namespace) {
		if(!strpos($namespace, '\\')){
			return false;
		}
		return str_replace('\\', '.', $namespace);
	}

    /**
     * @return WebApplication|null
     */
	public static function getInstance() : WebApplication{
		static $instance = null;
		if($instance === null){ $instance = new WebApplication(); }
		return $instance;
	}
	
}