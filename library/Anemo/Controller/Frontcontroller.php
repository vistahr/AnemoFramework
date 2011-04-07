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

namespace Anemo\Controller;

use Anemo\Application\Http;

/**
 * The frontcontroller is the main controller. Every request routes at first to the frontcontroller which calls futher controllers.
 * @author vince
 * @version 1.0
 */
class Frontcontroller
{
	
	private static $instance = null;
	
	private $config 	= null;
	
	private $request 	= null;
	private $response 	= null;
	
	private $router		= null;
	
	private $moduleDirectory;
	private $layoutTemplate;
	
	// Errorhandling
	private $errorController;
	private $errorAction; 
	
	
	private function __construct() {}
	private function __clone() {}
	
	
	public function init(array $config) {
		$this->config = $config;
		
		$this->errorController 	= $this->config['application']['errorhandling']['errorController'];
		$this->errorAction		= $this->config['application']['errorhandling']['errorAction'];
		$this->moduleDirectory 	= $this->config['application']['moduleDirectory'];
		$this->layoutTemplate	= $this->config['application']['layoutTemplate'];
		
		
		$this->request  = Http\Request::getInstance();
		$this->request->init();
		
		$this->response = Http\Response::getInstance();
		$this->response->setCharset = $this->config['application']['charset'];
		
		$this->router   = new \Anemo\Router();
		$this->router->loadRoutes($this->config[$this->config['application']['router']['routesConfig']]);
		
		return $this;
	}
	
	
	public static function getInstance(){
		if(self::$instance === null){
	    	self::$instance = new Frontcontroller();
	    }

	    return self::$instance;
	}
	
	public function getConfig() {
		return $this->config;
	}
	

	public function setModuleName($module) {
		$this->getRequest()->setModuleName($module);
		return $this;
	}
	public function getModuleName() {
		return $this->getRequest()->getModuleName();
	}
	
	public function setControllerName($controller) {
		$this->getRequest()->setControllerName($controller);
		return $this;
	}
	public function getControllerName() {
		return $this->getRequest()->getControllerName();
	}
	
	public function setActionName($action) {
		$this->getRequest()->setActionName($action);
		return $this;
	}
	public function getActionName() {
		return $this->getRequest()->getActionName();
	}
	
	
	
	public function getRouter() {
		return $this->router;
	}
	public function getRequest() {
		return $this->request;	
	}
	public function getResponse() {
		return $this->response;
	}
	
	
	public function getResource($resource) {
		if(($bootstrap = \Anemo\Registry::get('bootstrap')) != null)
			return $bootstrap->getResource($resource);
		return null;
	}

	
	public function getBasePath() {
		$basePath = $this->getRequest()->getServer("SCRIPT_NAME");
		if(preg_match('#/[a-z_-]+.php#',$basePath))
			$basePath = preg_replace('#/[a-z_-]+.php#','',$basePath);
		
		return $basePath;
	}
	
	
	public function getLayoutPath() {
		$layoutPath = $this->getModuleDirectory() . $this->getModuleName() . $this->layoutTemplate;
		if(!is_file($layoutPath))
			throw new Exception('Layouttemplate ' . $layoutPath . ' not found');
		
		return $layoutPath;
	}
	public function setLayoutTemplate($template) {
		$this->layoutTemplate = $template;
		return $this;
	}
	
	public function getModuleDirectory() {
		$dir =  ROOT . $this->moduleDirectory;
		if(!is_dir($dir))
			throw new Exception('Moduledirectory ' . $dir . ' not found');
		
		return $dir;
	}
	public function setModuleDirectory($dir) {
		$this->moduleDirectory = $dir;
		return $this;
	}
	
	public function getControllerDirectory() {
		return ROOT . $this->moduleDirectory . $this->getModuleName() . '/controllers';
	}
	
	public function setErrorController($controller) {
		$this->errorController = $controller;
		return $this;
	}
	public function setErrorAction($action) {
		$this->errorAction = $action;
		return $this;
	}
	
	public function initErrorHandling() {
		if($this->errorController == '' || $this->errorAction == '')
			throw new Exception('Errorcontroller: ' . $this->errorController . ' or Erroraction: ' . $this->errorAction . ' not specified');
		
		if($this->getResponse()->getStatus() != 0)
			$this->errorAction = 'error' . $this->getResponse()->getStatus();
			
		$this->setModuleName($this->getModuleName())
			 ->setControllerName($this->errorController)
			 ->setActionName($this->errorAction);
		return $this;
	}
	

	private function route(Http\Request $request, Http\Response $response) {
		$route = $this->getRouter()->route($request,$response);
		return $this;
	}
	
	
	public function execute() {
		$controllerClass = ucfirst(strtolower($this->getControllerName())) . 'Controller';
		
		if($controllerClass == '')
			throw new Exception('Controller ' . $controllerClass . ' not found.');
		
		$controller = new $controllerClass;
		$content = call_user_func(array($controller,'executeAction'),strtolower($this->getActionName()));
		
		return $content;
	}
	
	
	public function dispatch() {
		if($this->getModuleName() == "" || ($this->getModuleName() == "" && $this->getControllerName() == "" && $this->getActionName() == ""))
			$this->route($this->getRequest(),$this->getResponse());
		
		if(($bootstrap = \Anemo\Registry::get('bootstrap')) != null) {
			$layout = $bootstrap->getResource('layout');
			$layout->setLayout($this->getLayoutPath());
			$layout->setContent($this->execute());
			
			$this->getResponse()->replaceContent($layout->getResponse());
			
		} else {
			$this->getResponse()->replaceContent($this->execute());
		}
		
		return $this->getResponse();
	}
	
	
}









