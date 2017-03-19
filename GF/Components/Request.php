<?php
/**
 * This class represents the application request.
 * @package GF\Components
 * @author  Jorge Alejandro Quiroz Serna <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2017 jakolab
 */
namespace GF\Components;

use Exception;
use GF\System;

class Request {
	const TP_INVALID = 0;
	const TP_CLOSURE = 1;
	const TP_CONTROLLER = 2;
    /**
     * @var callable
     */
	private $closure;
    /**
     * @var \GF\Components\AppComponent
     */
	public $activeComponent;
    /**
     * @var string
     */
	private $action;
    /**
     * @var array
     */
	private $params = [];
    /**
     * @var array
     */
	private $dispatched;
    /**
     * @var int
     */
	private $typeOfRequest = 0;
    /**
     * @var \GF\Components\Response
     */
	private $response;

	public function __construct(array $dispatchedRoute, Response &$response){
		$this->dispatched = $dispatchedRoute;
		$this->response = $response;
		$this->fetchRequest();
	}

    /**
     * Runs the request.
     * @throws Exception if the request is not valid.
     */
	public function run(){
		if($this->typeOfRequest == self::TP_CLOSURE){
			$this->processClosure();
		} else if($this->typeOfRequest == self::TP_CONTROLLER){
			$this->processController();
		} else {
			throw new Exception("Invalid request");
		}
	}

    /**
     * Logic to execute the controller.
     */
	private function processController(){
        call_user_func_array([$this->activeComponent, $this->action], $this->params);
    }

    /**
     * Logic to execute a closure if the request is a callable
     */
    private function processClosure(){
	    $this->response->setContent($this->capture(function(){
            call_user_func_array($this->closure, $this->params);
        }));
    }

    /**
     * Capture the content of a callable.
     * @param callable $callback
     * @return string
     */
    private function capture(callable $callback) : string{
        ob_start();
        $callback();
        return ob_get_clean();
    }

    /**
     * Captures the type of request.
     */
	private function fetchRequest(){
		if($this->checkIfClosure()){ $this->typeOfRequest = self::TP_CLOSURE; }
        else if($this->checkIfController()){ $this->typeOfRequest = self::TP_CONTROLLER; }
	}

    /**
     * Checks if the request is a closure.
     * @return bool
     */
	private function checkIfClosure(){
		if(is_callable($this->dispatched['object'])){
			$this->closure = $this->dispatched['object'];
			$this->params = array_merge([$this->response], $this->dispatched['params']);
			return true;
		}
		return false;
	}

    /**
     * Checks if the request is a controller call.
     * @return bool
     */
	private function checkIfController(){
		if(isset($this->dispatched['object']) && $object = $this->loadController($this->dispatched['object'])){
			$this->activeComponent = $object;
			$this->action = $this->dispatched['action'];
			$this->params = $this->dispatched['params'];
			return true;
		}
		return false;
	}

    /**
     * Loads a controller.
     * @param string $path
     * @return bool|AppController
     */
	private function loadController(string $path){
        $object = $this->loadObject($path);
        return $object instanceof \GF\Components\AppController? $object : false;
    }

    /**
     * Loads any application component.
     * @param string $path
     * @return AppComponent|bool
     */
	private function loadObject(string $path){
	    if(!System::fileExists($path)){ return false; }
	    System::import($path);
	    $namespace = System::toNamespace($path);
	    $ID = substr($path, strrpos($path, '.') + 1);
	    $object = new $namespace($ID, $this->response, $this);
	    if(!$object instanceof AppComponent){
	        throw new Exception("Not a valid component");
        }
        $object->init();
        return $object;
    }
}