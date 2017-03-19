<?php
/**
 * This class is the template engine generator.
 * @package GF\Web\FangTE
 * @author  Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version  1.0.0
 * @copyright (c) 2017, jakolab
 */
namespace GF\Web\FangTE;

use GF\Components\WebComponent;
use GF\System;

class Fang extends WebComponent{
    /**
     * Base directory of compile files.
     * @var string
     */
    private $baseDir = "";
    /**
     * Directory to compile.
     * @var string
     */
    private $compileDir = "";
    /**
     * Name of the current processed file.
     * @var string
     */
    private $processedFileName = "";
    /**
     * Absolute path of the source file.
     * @var string
     */
    private $realSrcPath;
    /**
     * Absolute path of the output file.
     * @var string
     */
    private $realDesPath;
    /**
     * Outputted content of the processed file.
     * @var string
     */
    private $content;

    public function __construct(string $baseDir = ""){
        $this->baseDir = $baseDir != ''? $baseDir : "Cache.Build";
    }

    /**
     * Initializes the component.
     */
    public function init(){
        System::dir($this->baseDir);
    }

    /**
     * Compiles the file.
     * @param string $src
     */
    public function compile(string $src){
        $this->processedFileName = $this->getFileName($src);
        $this->realSrcPath = System::resolvePath($src, true, 'fang');
        $this->realDesPath = System::resolvePath("$this->compileDir.$this->processedFileName", true);
        $this->content = $this->getFile();
        $this->filterContent();
        $this->createFile();
    }

    /**
     * Returns the absolute path of the processed file.
     * @return string
     */
    public function getCompileFile(){
        return $this->realDesPath;
    }

    /**
     * This process an executes the filters to the content.
     */
    private function filterContent(){
        $this->interpolation();
        $this->structures();
    }

    /**
     * Filters all the interpolations.
     */
    private function interpolation(){
        $pattern = '/\{\{(.*)\}\}/';
        $this->content = preg_replace_callback($pattern, function($match){
            return "<?=" . $match[1] . "?>";
        }, $this->content);
    }

    /**
     * Filters all the structures, conditionals and loops.
     */
    private function structures(){
        $pattern = '/\@\:(.*)\:\@/';
        $this->content = preg_replace_callback($pattern, function($match){
            return "<?php" . $match[1] . "?>";
        }, $this->content);
    }

    private function createFile(){
        $h = fopen($this->realDesPath, 'w');
        fwrite($h, $this->content);
        fclose($h);
    }

    /**
     * Returns the content of the file to be processed.
     * @return string
     */
    private function getFile(){
        return file_get_contents($this->realSrcPath);
    }

    /**
     * Extracts the name of the file to be compiled.
     * @param string $path
     * @return string
     */
    private function getFileName($path){
        return substr($path, strrpos($path, '.') + 1);
    }

    /**
     * Creates the directory of the files to be compiled.
     * @param string $path
     */
    public function addDir($path){
        $fullPath = "$this->baseDir.$path";
        $this->compileDir = $fullPath;
        if(System::fileExists($fullPath)){ return; }
        $parts = explode(".", $path);
        $currentDir = $this->baseDir;
        foreach($parts AS $dir){
            System::dir("$currentDir.$dir");
            $currentDir .= ".$dir";
        }
    }
}