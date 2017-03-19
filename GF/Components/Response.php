<?php
/**
 * This class represents the application response.
 * @package GF
 * @author  Jorge Alejandro Quiroz Serna <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2017 jakolab
 */
namespace GF\Components;

class Response {
    const STATUS_OK = 200;
    const STATUS_BAD = 400;
    const STATUS_SERVER_ERR = 500;

    /**
     * Content of the response.
     * @var string
     */
    private $content;
    /**
     * Headers of the response.
     * @var array
     */
    private $headers = [];
    /**
     * status of the response.
     * @var int
     */
    private $status = 200;

    /**
     * Allows to set the content of the request from the outside.
     * @param string $content
     */
    public function setContent(string $content){
        $this->content = $content;
    }

    /**
     * Allows to append text to the content.
     * @param string $content
     */
    public function append(string $content){
        $this->content .= $content;
    }

    /**
     * Allows to preppend text to the content.
     * @param string $content
     */
    public function prepend(string $content){
        $this->content = $content . $this->content;
    }

    /**
     * Returns the response text.
     * @return string
     */
    public function getContent(){
        return $this->content;
    }

    /**
     * Allows to set headers to the response.
     * @param string $header
     * @param string $value
     */
    public function header(string $header, string $value = ""){
        if($value != ""){
            $this->headers[$header] = $value;
        } else {
            $this->headers[] = $header;
        }
    }

    /**
     * Injects the headers to the response.
     */
    private function insertHeaders(){
        foreach($this->headers AS $h=>$v){
            if(is_string($h)){
                header("$h: $v");
            } else {
                header($v);
            }
        }
    }

    /**
     * Prints the response.
     */
    public function send(){
        http_response_code($this->status);
        $this->insertHeaders();
        echo $this->content;
    }
}