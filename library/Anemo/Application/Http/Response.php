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

/**
 * Response is a singleton and represent the outgoing response
 * @author vince
 * @version 1.0
 */
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
	
	/**
  	 * Singleton
  	 * @return \Anemo\Response
  	 */
	public static function getInstance() {
		if(self::$instance === null){
	    	self::$instance = new Response();
	    }
	    return self::$instance;
	}
	
	/**
	 * Append a new header for the http response
	 * @param string $name
	 * @param string $content
	 * @return \Anemo\Response
	 */
	public function addHeader($name, $content) {
    	$this->headers[$name] = $content;
    	return $this;
  	}
	
  	/**
  	 * Set the status of the the http response
  	 * @param string $status
  	 * @return void
  	 */
	public function setStatus($status) {
    	$this->status = $status;
  	}
  	
  	/**
  	 * Return the status
  	 * @return string
  	 */
  	public function getStatus() {
  		return $this->status;
  	}
  	
  	/**
  	 * Set the content type of the http response
  	 * @param string $contentType
  	 * @return void
  	 */
  	public function setContentType($contentType) {
  		$this->contentType = $contentType;
  	}
  	
  	/**
  	 * Set the charset of the http response
  	 * @param string $charset
  	 * @return void
  	 */
  	public function setCharset($charset) {
  		$this->charset = $charset;
  	}
  	
  	/**
  	 * Return the content
  	 * @return string
  	 */
  	public function getContent() {
    	return $this->content;
  	}  
	
  	/**
  	 * Replace the content. Overwrites all existing data
  	 * @param string $newContent
  	 * @return void
  	 */
  	public function replaceContent($newContent) {
    	$this->content = $newContent;
  	}
  	
  	/**
  	 * Set a exception and the statuscode
  	 * @param \Anemo\Runtime\Exception $exception
  	 * @return void
  	 */
  	public function setException(\Anemo\Runtime\Exception $exception) {
  		$this->exception = $exception;
  		$this->status	 = $this->exception->getCode();
  	}
  	
  	/**
  	 * Return the exception object
  	 * @return \Anemo\Runtime\Exception
  	 */
  	public function getException() {
  		return $this->exception;
  	}
  	
  	/**
  	 * Check if the respoinse has an exception
  	 * @return boolean
  	 */
  	public function hasException() {
  		return isset($this->exception);
  	}
	
  	/**
  	 * Send the http request to the browser and output the data with the given headers
  	 * @throws Exception
  	 * @return void
  	 */
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