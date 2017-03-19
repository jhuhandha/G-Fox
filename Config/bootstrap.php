<?php 
/**
 * This file contains all the logic to initiate the application.
 * @package Config
 * @author  Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version  1.0.0 
 * @copyright (c) 2017, jakolab
 */

require "globals.php";
require VENDOR_DIR . DS . "autoload.php";
require KERNEL_DIR . DS . "System.php";

\GF\System::createApp()->start();