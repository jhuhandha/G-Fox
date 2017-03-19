<?php
/**
 * You can use this class to implement functionality to be executed in all controllers.
 * @package GF
 * @author  Jorge Alejandro Quiroz Serna <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2017 jakolab
 */
namespace Controllers;

use GF\Components\AppController;

class Controller extends AppController{

    /**
     * Use this function instead of the constructor.
     */
    public function init() {
        parent::init();
    }

}