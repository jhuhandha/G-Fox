<?php
/**
 * This class is the base of every application controller.
 * @package GF\Components
 * @author  Jorge Alejandro Quiroz Serna <alejo.jko@gmail.com>
 * @version 1.0.1
 * @copyright (c) 2017 jakolab
 */

namespace GF\Components;

use GF\Web\FangTE\Fang;

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
    /**
     * @var Fang
     */
    protected $fang;

    public function __construct(string $ID, Response &$response,  Request &$request){
        $this->ID = $ID;
        $this->request = $request;
        $this->response = $response;
        $this->fang = new Fang();
    }

    /**
     * Initializes the controller.
     */
    public function init(){
        $this->layoutsPath = 'Layouts';
        $this->viewsPath = "Views.$this->ID";
        $this->fang->init();
        $this->fang->addDir($this->viewsPath);
    }

    /**
     * Render a view
     * @param string $view
     * @param array $params
     */
    protected function render(string $view, array $params = []){
        $viewPath = "$this->viewsPath.$view";
        $this->fang->compile($viewPath);
        $this->viewContent = $this->capture($this->fang->getCompileFile(), $params);
        $this->loadLayout();
        $this->response->setContent($this->content);
    }

    /**
     * Loads the content from the file.
     */
    private function loadLayout(){
        $layoutPath = "$this->layoutsPath.$this->layout";
        $this->fang->compile($layoutPath);
        $this->content = $this->capture($this->fang->getCompileFile());
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
        include $filePath;
        return ob_get_clean();
    }
}