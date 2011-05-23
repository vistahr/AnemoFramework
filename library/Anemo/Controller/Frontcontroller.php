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
	
	/**
	 * init the singleton frontcontroller. Loads the config files, and init the request, response and the router
	 * @param array $config
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
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
	
	/**
	 * Return the self instance of the singleton
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
	public static function getInstance(){
		if(self::$instance === null){
	    	self::$instance = new Frontcontroller();
	    }

	    return self::$instance;
	}
	
	/**
	 * Return the config array
	 * @return array
	 */
	public function getConfig() {
		return $this->config;
	}
	
	/**
	 * Wrapper for setModuleName from the request class
	 * @param string $module
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
	public function setModuleName($module) {
		$this->getRequest()->setModuleName($module);
		return $this;
	}
	
	/**
	 * Wrapper for getModuleName from the request class
	 * @return string
	 */
	public function getModuleName() {
		return $this->getRequest()->getModuleName();
	}
	
	/**
	 * Wrapper for setControllerName from the request class
	 * @param string $controller
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
	public function setControllerName($controller) {
		$this->getRequest()->setControllerName($controller);
		return $this;
	}
	
	/**
	 * Wrapper for getControllerName from the request class
	 * @return string
	 */
	public function getControllerName() {
		return $this->getRequest()->getControllerName();
	}
	
	/**
	 * Wrapper for setActionName from the request class
	 * @param string $action
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
	public function setActionName($action) {
		$this->getRequest()->setActionName($action);
		return $this;
	}
	
	/**
	 * Wrapper for getActionName from the request class
	 * @return string
	 */
	public function getActionName() {
		return $this->getRequest()->getActionName();
	}
	
	/**
	 * Return the router object
	 * @return \Anemo\Router
	 */
	public function getRouter() {
		return $this->router;
	}
	
	/**
	 * Return the request object
	 * @return \Anemo\Application\Http\Request
	 */
	public function getRequest() {
		return $this->request;	
	}
	
	/**
	 * Return the response object
	 * @return \Anemo\Application\Http\Response
	 */
	public function getResponse() {
		return $this->response;
	}
	
	/**
	 * Return the, if exists, given resource
	 * @param string $resource
	 * @return object
	 */
	public function getResource($resource) {
		if(($bootstrap = \Anemo\Registry::get('bootstrap')) != null)
			return $bootstrap->getResource($resource);
		return null;
	}

	/**
	 * Return the base path
	 * @return string
	 */
	public function getBasePath() {
		$basePath = $this->getRequest()->getServer("SCRIPT_NAME");
		if(preg_match('#/[a-z_-]+.php#',$basePath))
			$basePath = preg_replace('#/[a-z_-]+.php#','',$basePath);
		
		return $basePath;
	}
	
	/**
	 * Return the layout path. Module directory / module name / temp.late
	 * @throws Exception
	 * @return string
	 */
	public function getLayoutPath() {
		$layoutPath = $this->getModuleDirectory() . $this->getModuleName() . $this->layoutTemplate;
		if(!is_file($layoutPath))
			throw new Exception('Layouttemplate ' . $layoutPath . ' not found');
		
		return $layoutPath;
	}
	
	/**
	 * Set the layout template. An extension is required
	 * @param string $template
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
	public function setLayoutTemplate($template) {
		$this->layoutTemplate = $template;
		return $this;
	}
	
	/**
	 * Return the module directory. ROOT / module directory
	 * @throws Exception
	 * @return string module directory
	 */
	public function getModuleDirectory() {
		$dir =  ROOT . $this->moduleDirectory;
		if(!is_dir($dir))
			throw new Exception('Moduledirectory ' . $dir . ' not found');
		
		return $dir;
	}
	
	/**
	 * Set the module directory
	 * @param string $dir
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
	public function setModuleDirectory($dir) {
		$this->moduleDirectory = $dir;
		return $this;
	}
	
	/**
	 * Return the controller directory. ROOT / module directory / module name / "controllers"
	 */
	public function getControllerDirectory() {
		return ROOT . $this->moduleDirectory . $this->getModuleName() . '/controllers';
	}
	
	/**
	 * Set the error controller
	 * @param string $controller
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
	public function setErrorController($controller) {
		$this->errorController = $controller;
		return $this;
	}
	
	/**
	 * Set the error action
	 * @param string $action
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
	public function setErrorAction($action) {
		$this->errorAction = $action;
		return $this;
	}
	
	/**
	 * Init the error handling. Check if error controller & action is set and set them as controller and action name
	 * @throws Exception
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
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
	
	/**
	 * Route the request and response
	 * @param Http\Request $request
	 * @param Http\Response $response
	 * @return \Anemo\Application\Http\Frontcontroller
	 */
	private function route(Http\Request $request, Http\Response $response) {
		$route = $this->getRouter()->route($request,$response);
		return $this;
	}
	
	/**
	 * Execute the controller and action which are set and return the executed content
	 * @throws Exception
	 * @return string
	 */
	public function execute() {
		$controllerClass = ucfirst(strtolower($this->getControllerName())) . 'Controller';
		
		if($controllerClass == '')
			throw new Exception('Controller ' . $controllerClass . ' not found.');
		
		$controller = new $controllerClass;
		$content = call_user_func(array($controller,'executeAction'),strtolower($this->getActionName()));
		
		return $content;
	}
	
	/**
	 * The dispatcher routes the request, run the modulebootstrap, get the layout, execute the request, insert it into the layout, write the response object and return the whole response
	 * @return string
	 */
	public function dispatch() {
		if($this->getModuleName() == "" || ($this->getModuleName() == "" && $this->getControllerName() == "" && $this->getActionName() == ""))
			$this->route($this->getRequest(),$this->getResponse());
		
		// Modulebootstrap
		include_once ROOT . $this->config['application']['moduleDirectory'] . $this->getModuleName() . '/Bootstrap.php';
		$moduleBootstrapClassName = ucfirst($this->getModuleName()) . 'Bootstrap';
		$moduleBootstrap 		  = new $moduleBootstrapClassName();
		$moduleBootstrap->bootstrap();
		
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









