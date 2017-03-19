<?php
/**
 * This class is the base of every application controller.
 * @package GF\Components
 * @author  Jorge Alejandro Quiroz Serna <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2017 jakolab
 */

namespace GF\Components;

use GF\System;

abstract class AppController extends AppComponent {
    /**
     * Name of the layout to be used.
     * @var string
     */
    protected $layout = 'default';
    /**
     * Layouts folder path.
     * @var string
     */
    protected $layoutsPath;
    /**
     * Views folder path.
     * @var string
     */
    protected $viewsPath;
    /**
     * The processed content from the view.
     * @var string
     */
    protected $viewContent;
    /**
     * The processed content from the layout.
     * @var string
     */
    protected $content;

    public function __construct(string $ID, Response &$response,  Request &$request){
        $this->ID = $ID;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Initializes the controller.
     */
    public function init(){
        $this->layoutsPath = 'Layouts';
        $this->viewsPath = "Views.$this->ID";
    }

    /**
     * Render a view
     * @param string $view
     * @param array $params
     * @throws \Exception if the path to the file doesn't exists.
     */
    protected function render(string $view, array $params = []){
        $viewPath = "$this->viewsPath.$view";
        if(!System::fileExists($viewPath)){
            throw new \Exception("The view '$view' does not exists for controller '$this->ID'");
        }
        $this->viewContent = $this->capture($viewPath, $params);
        $this->loadLayout();
        $this->response->setContent($this->content);
    }

    /**
     * Loads the content from the file.
     * @throws \Exception If the path to the layout doesn't exists.
     */
    private function loadLayout(){
        $layoutPath = "$this->layoutsPath.$this->layout";
        if(!System::fileExists($layoutPath)){
            throw new \Exception("The layout '$this->layout' does not exists.");
        }
        $this->content = $this->capture($layoutPath);
    }

    /**
     * Captures the content of a file
     * @param string $filePath
     * @param array $params
     * @return string
     */
    private function capture(string $filePath, array $params = []){
        ob_start();
        foreach($params AS $k=>$v){  $$k = $v; }
        include System::resolvePath($filePath, true);
        return ob_get_clean();
    }
}