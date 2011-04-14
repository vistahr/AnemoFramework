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
 * Request is a singleton and represent the incoming request
 * @author vince
 * @version 1.0
 */
class Request
{

	public static $instance = null;
	
	protected $post;
  	protected $get;
  	protected $cookie;
  	protected $file;
  	protected $header;
  	protected $server;
  	
  	protected $params = array();
  	
  	protected $ajax = false;
  	
  	protected $module 		= '';
	protected $controller 	= '';
	protected $action 		= ''; 
  	
	
	private function __construct() {}
	
	/**
	 * Init the Request, save the global php arrays by reference and save the HTTP_ key from the server array
	 * @return void
	 */
	public function init() {
    	$this->post =   &$_POST;
    	$this->get  =   &$_GET;
    	$this->cookie = &$_COOKIE;
    	$this->file =   &$_FILES;

    	foreach($_SERVER as $key => $value) {
      		if(substr($key, 0, 5) == 'HTTP_') {
        		$this->header[$key] = $value;
      		} else {
      			$this->server[$key] = $value;
      		}
    	}
    	
    	if($this->getHeader('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest')
    		$this->ajax = true;
  	}
	
  	/**
  	 * Singleton
  	 * @return \Anemo\Request
  	 */
	public static function getInstance(){
		if(self::$instance === null)
	    	self::$instance = new Request();
	    
	    return self::$instance;
	}
	
	/**
	 * Set module
	 * @param string $module
	 * @return \Anemo\Request
	 */
	public function setModuleName($module) {
		$this->module = $module;
		return $this;
	}
	
	/**
	 * Get module
	 * @return string
	 */
	public function getModuleName() {
		return $this->module;
	}
	
	/**
	 * Set controller
	 * @param string $controller
	 * @return \Anemo\Request
	 */
	public function setControllerName($controller) {
		$this->controller = $controller;
		return $this;
	}
	
	/**
	 * Get controller
	 * @return string
	 */
	public function getControllerName() {
		return $this->controller;
	}
	
	/**
	 * Write a non camel case string to an cc string
	 * @param string $action
	 * @return string
	 */
	public function toCamelCase($action) {
		if($parts = explode('-', $action)) {
			$parts = $parts ? array_map('ucfirst', $parts) : array($action);
		    $parts[0] = lcfirst($parts[0]);
		    $action = implode('', $parts);
		    return $action;
		}
		return $action;
	}
	
	/**
	 * Set action
	 * @param string $action
	 * @return \Anemo\Request
	 */
	public function setActionName($action) {
		$this->action = $action;//$this->toCamelCase($action);
		return $this;
	}
	
	/**
	 * Get action
	 * @return string
	 */
	public function getActionName() {
		return $this->action;
	}
	
	/**
	 * Check if the key is in the internal server array
	 * @param string $key
	 * @return boolean
	 */
	public function issetServer($key){
     	return (isset($this->server[$key]));
  	}
  	
  	/**
  	 * Get the value of the key by the server array
  	 * @param string $key
  	 * @return string
  	 */
  	public function getServer($key){
    	if($this->issetServer($key)) {
      		return $this->server[$key];
    	}
    	return null;
  	}
  	
  	/**
  	 * Return the whole array
  	 * @return array
  	 */
  	public function getServerArray() {
  		return $this->server;
  	}
  	
	/**
	 * Check if the key is inside the header array
	 * @param string $key
	 * @return boolean
	 */
	public function issetHeader($key){
     	return (isset($this->header[$key]));
  	}
  	
  	/**
  	 * Return the value of the key inside the header array
  	 * @param string $key
  	 * @return string
  	 */
  	public function getHeader($key){
    	if($this->issetHeader($key)) {
      		return $this->header[$key];
    	}
    	return null;
  	}
  	
  	/**
  	 * Check if the key is inside the paray array
  	 * @param string $key
  	 * @return boolean
  	 */
  	public function issetParam($key) {
  		return (isset($this->param[$key]));
  	}
  	
  	/**
  	 * Return the value of the key, from the param array
  	 * @param unknown_type $key
  	 * @return string
  	 */
  	public function getParam($key) {
    	if($this->issetParam($key)) {
      		return $this->param[$key];
    	}
    	return null;
  	} 
  	
  	/**
  	 * Append a new params array
  	 * @param array $params
  	 * @return void
  	 */
  	public function setParams(array $params) {
  		$this->params = array_merge($this->params,$params);
  	} 	
  	
	/**
	 * Check if the key has a value inside the get array
	 * @param string $key
	 * @return boolean
	 */
  	public function issetGet($key = null) {
  		if($key === null)
  			return (isset($this->get) && count($this->get) > 0);
  			
    	return isset($this->get[$key]);
  	}
	
  	/**
  	 * Return the value of the key from the get array
  	 * @param string $key
  	 * @return string
  	 */
  	public function getGet($key = null) {
  		if($key === null)
  			return $this->get;
  		
    	if($this->issetGet($key))
      		return $this->get[$key];
		
    	return null;
  	}
  	
  	/**
  	 * Set a new get parameter
  	 * @param string $key
  	 * @param string $value
  	 * @return void
  	 */
  	public function setGet($key,$value) {
  		$this->get[$key] = $value;
  	}

  	/**
  	 * Check if the key has a value inside the post array
  	 * @param string $key
  	 * @return boolean
  	 */
  	public function issetPost($key = null) {
  		if($key === null)
  			return (isset($this->post) && count($this->post) > 0);
  		
    	return isset($this->post[$key]);
  	}
  	
  	/**
  	 * Return the value of the key from the post array
  	 * @param string $key
  	 * @return string
  	 */
  	public function getPost($key = null) {
  		if($key === null)
  			return $this->post;
  			
    	if($this->issetPost($key))
      		return $this->post[$key];
    	
    	return null;
  	}

  	/**
  	 * Check if the key has a value inside the file array
  	 * @param string $key
  	 * @return boolean
  	 */
  	public function issetFile($key = null) {
  		if($key === null)
  			return (isset($this->file) && count($this->file) > 0);
  		
    	return isset($this->file[$key]);
  	}
	
  	/**
  	 * Return the value of the key from the file array
  	 * @param string $key
  	 * @return string
  	 */
  	public function getFile($key) {
    	if($this->issetFile($key)) {
      		return $this->file[$key];
    	}
    	return null;
  	}

  	/**
  	 * Check if the key has a value inside the cookie array
  	 * @param string $key
  	 * @return boolean
  	 */
  	public function issetCookie($key) {
    	return (isset($this->cookie[$key]));
  	}
  	
  	/**
  	 * Return the value of the key from the cookie array
  	 * @param string $key
  	 * @return string
  	 */
  	public function getCookie($key) {
    	if($this->issetCookie($key)) {
      		return $this->cookie[$key];
    	}
    	return null;
  	}
  	
  	/**
  	 * Return true if the request is an ajax request, else false
  	 * @return boolean
  	 */
  	public function isAjax() {
  		return $this->ajax;
  	}

}