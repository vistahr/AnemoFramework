<?php
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
		if(self::$instance === null){
	    	self::$instance = new Request();
	    }
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
	
	public function setActionName($action) {
		$this->action = $action;
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
  	

  	public function issetGet($key) {
    	return (isset($this->get[$key]));
  	}

  	public function getGet($key) {
    	if($this->issetGet($key)) {
      		return $this->get[$key];
    	}
    	return null;
  	}
  	public function getGetArray() {
  		return $this->get;
  	}
  	public function setGet($key,$value) {
  		$this->get[$key] = $value;
  	}

  	
  	public function issetPost($key) {
    	return (isset($this->post[$key]));
  	}
  	public function getPost($key) {
    	if($this->issetPost($key)) {
      		return $this->post[$key];
    	}
    	return null;
  	}

  	
  	public function issetFile($key) {
    	return (isset($this->file[$key]));
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