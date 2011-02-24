<?php
namespace Anemo\Application\Http;

class Response
{

	public static $instance = null;
	
	private $headers 		= array();
	private $content 		= '';
	private $contentType 	= 'text/html';
	private $charset		= 'utf-8';
	private $status  		= '200 OK';
	
	private $exception = null;
	
	private function __construct(){}
	
	
	public static function getInstance() {
		if(self::$instance === null){
	    	self::$instance = new Response();
	    }
	    return self::$instance;
	}
	
	
	public function addHeader($name, $content) {
    	$this->headers[$name] = $content;
    	return $this;
  	}
	
	public function setStatus($status) {
    	$this->status = $status;
  	}
  	public function getStatus() {
  		return $this->status;
  	}
  	
  	public function setContentType($contentType) {
  		$this->contentType = $contentType;
  	}
  	public function setCharset($charset) {
  		$this->charset = $charset;
  	}
  	
  	public function getContent() {
    	return $this->content;
  	}  

  	public function replaceContent($newContent) {
    	$this->content = $newContent;
  	}
  	
  	public function setException(\Anemo\Runtime\Exception $exception) {
  		$this->exception = $exception;
  		$this->status	 = $this->exception->getCode();
  	}
  	public function getException() {
  		return $this->exception;
  	}
  	public function hasException() {
  		return isset($this->exception);
  	}
	
  	
  	public function send() {
  		
  		if(headers_sent() && !$this->hasException())
  			throw new Exception('Header already sent.');
  		
  		if(!headers_sent())
	    	header('HTTP/1.1 '.$this->status);

	    if(!in_array('Content-type',$this->headers))
	    	$this->headers['Content-type'] = $this->contentType . '; charset=' . $this->charset;
	    	
	    foreach($this->headers as $name => $content) {
	    	header($name.': '.$content);
	    }
	    
	    echo $this->getContent();
	
	    // resetten
	    $this->content = '';
	    $this->headers = null;
  	}
  	

}