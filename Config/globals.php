<?php 
/**
 * This file contains all the constants needed in the application
 * @package Config
 * @author  Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version  1.0.0 
 * @copyright (c) 2017, jakolab
 */

define("DS", DIRECTORY_SEPARATOR);
define("ROOT_DIR", realpath(__DIR__ . '/../'));
define("APP_DIR", realpath(ROOT_DIR . DS . 'App'));
define("CACHE_DIR", realpath(ROOT_DIR . DS . 'Cache'));
define("CONFIG_DIR", realpath(ROOT_DIR . DS. 'Config'));
define("DATABASE_DIR", realpath(ROOT_DIR . DS . 'Cache'));
define("KERNEL_DIR", realpath(ROOT_DIR . DS . 'GF'));
define("PUBLIC_DIR", realpath(ROOT_DIR . DS . 'Public'));
define("VENDOR_DIR", realpath(ROOT_DIR . DS . 'vendor'));