<?php

/**
 * 
 * 	Copyright 2011 Vince. All rights reserved.
 * 	
 * 	Redistribution and use in source and binary forms, with or without modification, are
 * 	permitted provided that the following conditions are met:
 * 	
 * 	   1. Redistributions of source code must retain the above copyright notice, this list of
 * 	      conditions and the following disclaimer.
 * 	
 * 	   2. Redistributions in binary form must reproduce the above copyright notice, this list
 * 	      of conditions and the following disclaimer in the documentation and/or other materials
 * 	      provided with the distribution.
 * 	
 * 	THIS SOFTWARE IS PROVIDED BY Vince ``AS IS'' AND ANY EXPRESS OR IMPLIED
 * 	WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * 	FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL Vince OR
 * 	CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * 	CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * 	SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * 	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * 	NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * 	ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 	
 * 	The views and conclusions contained in the software and documentation are those of the
 * 	authors and should not be interpreted as representing official policies, either expressed
 * 	or implied, of Vince.
 */

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