<?php 
/**
 * This class represents the system, it contains the running application and initializes
 * all the basic logic that every gf-web-application needs.
 *
 * @package GF
 * @author  Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version  1.0.0 
 * @copyright (c) 2017, jakolab
 */
namespace GF;

use Exception;

final class System {
	/**
	 * Contains the application directory aliases.
	 * @var array
	 */
	private static $aliases = [];
	/**
	 * The application instance.
	 * @var \GF\WebApplication
	 */
	private static $application = null;
	/**
	 * The current system version.
	 * @var string
	 */
	private static $version = "1.0.0";

	/**
	 * Creates the application instance
	 * @return \GF\WebApplication Application instance
	 */
	public static function createApp(){
		self::loadUtilities();
		self::import("GF.WebApplication");
		self::$application = \GF\WebApplication::getInstance();
		self::$application->init();
		return self::$application;
	}

	/**
	 * Import a file using the dots notation
	 * @param  mixed  	$resources 	Array | string path(s) of the file to be imported.
	 * @param  boolean 	$useAlias  	If the import should resolve the alias or is an 
	 *                              absolute path.
	 * @param boolean 	$once 		If the file should be included once
	 * @return mixed	boolean | mixed false if it cannot import the file, otherwise 
	 *                       it returns the filecontent.
	 */
	public static function import($resources, $useAlias = true, $once = true){
		if(is_string($resources)){
			return self::importSingle($resources, $useAlias, $once);
		} else if(is_array($resources)){
			return self::importMultimple($resources, $useAlias, $once);
		}
	}

	/**
	 * Import a single file, its used by the import function to make the proccess 
	 * easier.
	 * @param  string 	$resource 	path (dots notation or absolute).
	 * @param  boolean 	$useAlias 	If the function should resolve the path.
	 * @param boolean 	$once 		If the file should be included once
	 * @return mixed	boolean | mixed false if it cannot import the file, otherwise 
	 *                       it returns the filecontent.
	 */
	private static function importSingle($resource, $useAlias, $once){
		if($useAlias){ $resource = self::resolvePath($resource, true); }
		if(!file_exists($resource)){ return false; }
		return $once? include_once $resource : include $resource;
	}

	/**
	 * Import multiple files at once, its used by the import function to make 
	 * the proccess easier.
	 * @param  string[] $resources 	Collection of files to be imported.
	 * @param  boolean 	$useAlias  	If the function should resolve the path.
	 * @param boolean 	$once 		If the file should be included once
	 * @return mixed	boolean | mixed false if it cannot import the file, otherwise 
	 *                       it returns the filecontent.
	 */
	private static function importMultimple($resources, $useAlias, $once){
		$withoutError = true;
		foreach($resources AS $dir){
			if(!self::importSingle($dir, $useAlias, $once)){
				$withoutError = false;
				break;
			}
		}
		return $withoutError;
	}

	/**
	 * Loads common utilities for the system
	 */
	public static function loadUtilities(){
		# loading aliases
		$aliases = KERNEL_DIR . DS . "Utilities" . DS . "aliases.php";
		if(!file_exists($aliases)){ throw new Exception("Error loading aliases"); }
		self::$aliases = include_once $aliases;
		# loading functions
		$functions = KERNEL_DIR . DS . "Utilities" . DS . "functions.php";
		if(!file_exists($functions)){ throw new Exception("Error loading functions"); }
		include_once $functions;
	}

	/**
	 * Turns a dots notation path into a absolute path (See GF/utilities/aliases.php).
	 * @param  string  $path   Dots notation path.
	 * @param  boolean $isFile if the resolved path should contains an extension
	 * @param  string $extension
	 * @return string
	 */
	public static function resolvePath($path, $isFile = false, $extension = 'php'){
		$parts = explode('.', $path);
		$lastPart = end($parts);
		$finalPath = reset($parts);		
		if(key_exists($finalPath, self::$aliases)){
			$finalPath = self::$aliases[$finalPath];
			unset($parts[0]);
		}
		$dirs = count($parts);
		for($i = 1; $i < $dirs; $i ++) { $finalPath .= DS . $parts[$i]; }
		$finalPath .= ($lastPart !== "")? DS . $lastPart : '';
		return $finalPath . ($isFile? ".$extension" : "");
	}

    /**
     * This function converts a dots notation string to a namespace
     * @param string $path
     * @return bool|string
     */
	public static function toNamespace($path){
		if(strpos($path, ".") == false){ return false; }
		$path = str_replace(".", "\\", $path);
		return "\\" . ucfirst($path);
	}

	/**
	 * Returns the application running instance
	 * @return \GF\WebApplication
	 */
	public static function app(){
		return self::$application;
	}

	/**
	 * Checks if a file or directory exists.
	 * @param  string  $path   Dots notation path or absolute path.
	 * @param  boolean $isFile If file or directory
	 * @param  string  $ext    
	 * @return boolean
	 */
	public static function fileExists($path, $isFile = true, $ext = 'php'){
		if(strpos($path, "\\") != false){ $path = str_replace("\\", ".", $path); }
		if($isFile){ $rp = self::resolvePath($path, $isFile, $ext); }
		else { $rp = $path; }
		return file_exists($rp);
	}

	/**
	 * Returns the system version
	 * @return string
	 */
	public static function version(){
		return self::$version;
	}	
}