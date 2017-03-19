<?php
/**
 * This is the main controller of the application.
 * @package GF
 * @author  Jorge Alejandro Quiroz Serna <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2017 jakolab
 */
namespace Controllers;


class Main  extends Controller {
    public $name = "Class name";
    public function index(){
        $this->render('index', ['foo' => 'var']);
    }
}