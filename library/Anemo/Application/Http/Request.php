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
	
	public static function getInstance(){
		if(self::$instance === null)
	    	self::$instance = new Request();
	    
	    return self::$instance;
	}
	
	
	public function setModuleName($module) {
		$this->module = $module;
		return $this;
	}
	public function getModuleName() {
		return $this->module;
	}
	
	public function setControllerName($controller) {
		$this->controller = $controller;
		return $this;
	}
	public function getControllerName() {
		return $this->controller;
	}
	
	public function toCamelCase($action) {
		if($parts = explode('-', $action)) {
			$parts = $parts ? array_map('ucfirst', $parts) : array($action);
		    $parts[0] = lcfirst($parts[0]);
		    $action = implode('', $parts);
		    return $action;
		}
		return $action;
	}
	
	public function setActionName($action) {
		$this->action = $action;//$this->toCamelCase($action);
		return $this;
	}
	public function getActionName() {
		return $this->action;
	}
	
	
	public function issetServer($key){
     	return (isset($this->server[$key]));
  	}
  	public function getServer($key){
    	if($this->issetServer($key)) {
      		return $this->server[$key];
    	}
    	return null;
  	}
  	public function getServerArray() {
  		return $this->server;
  	}
  	
	
	public function issetHeader($key){
     	return (isset($this->header[$key]));
  	}
  	public function getHeader($key){
    	if($this->issetHeader($key)) {
      		return $this->header[$key];
    	}
    	return null;
  	}
  	
  	
  	public function issetParam($key) {
  		return (isset($this->param[$key]));
  	}
  	public function getParam($key) {
    	if($this->issetParam($key)) {
      		return $this->param[$key];
    	}
    	return null;
  	} 
  	public function setParams(array $params) {
  		$this->params = array_merge($this->params,$params);
  	} 	
  	

  	public function issetGet($key = null) {
  		if($key === null)
  			return (isset($this->get) && count($this->get) > 0);
  			
    	return isset($this->get[$key]);
  	}

  	public function getGet($key = null) {
  		if($key === null)
  			return $this->get;
  		
    	if($this->issetGet($key))
      		return $this->get[$key];
		
    	return null;
  	}
  	public function setGet($key,$value) {
  		$this->get[$key] = $value;
  	}

  	
  	public function issetPost($key = null) {
  		if($key === null)
  			return (isset($this->post) && count($this->post) > 0);
  		
    	return isset($this->post[$key]);
  	}
  	public function getPost($key = null) {
  		if($key === null)
  			return $this->post;
  			
    	if($this->issetPost($key))
      		return $this->post[$key];
    	
    	return null;
  	}

  	
  	public function issetFile($key = null) {
  		if($key === null)
  			return (isset($this->file) && count($this->file) > 0);
  		
    	return isset($this->file[$key]);
  	}

  	public function getFile($key) {
    	if($this->issetFile($key)) {
      		return $this->file[$key];
    	}
    	return null;
  	}

  	
  	public function issetCookie($key) {
    	return (isset($this->cookie[$key]));
  	}
  	public function getCookie($key) {
    	if($this->issetCookie($key)) {
      		return $this->cookie[$key];
    	}
    	return null;
  	}
  	
  	public function isAjax() {
  		return $this->ajax;
  	}

}