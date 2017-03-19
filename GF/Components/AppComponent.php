<?php
/**
 * This class is the base of every app component.
 * @package GF\Components
 * @author  Jorge Alejandro Quiroz Serna <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2017 jakolab
 */
namespace GF\Components;

abstract class AppComponent {
    /**
     * Identifier of the component.
     * @var string
     */
    protected $ID;
    /**
     * The component response.
     * @var \GF\Components\Response
     */
    protected $response;
    /**
     * The component request.
     * @var \GF\Components\Request
     */
    protected $request;

    /**
     * Initializes the component
     * @return mixed
     */
    abstract function init();
}