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

namespace Anemo;

/**
 * The router class routes incoming requests to the specified controllers and actions
 * @author vince
 * @version 1.0
 */
class Router 
{
	protected $routes = array();
	protected $requestData = array();
	

	protected $defaultController;
	protected $defaultAction;
	protected $defaultModule;
	
	protected $request;
	
	/**
	 * Initialize the router
	 */
	public function __construct() {
		$this->requestData = Controller\Router\Rewriter::rewriteRequest();
	}
	
	/**
	 * Set the default module, which will be requested when nothing other is setted
	 * @param string $module
	 * @return Anemo\Router
	 */
	public function setDefaultModule($module) {
		$this->defaultModule = $module;
		return $this;
	}
	/**
	 * Set the default controller, which will be requested when nothing other is setted
	 * @param string $controller
	 * @return Anemo\Router
	 */
	public function setDefaultController($controller) {
		$this->defaultController = $controller;
		return $this;
	}
	/**
	 * 
	 * Set the default action, which will be requested when nothing other is setted
	 * @param string $action
	 * @return Anemo\Router
	 */
	public function setDefaultAction($action) {
		$this->defaultAction = $action;
		return $this;
	}
	
	/**
	 * Load and set the default route
	 * @param array $config
	 * @return void
	 */
	public function loadRoutes(array $config) {
		$this->setDefaultModule($config['']['module'])
			 ->setDefaultController($config['']['controller'])
			 ->setDefaultAction($config['']['action']);
		
		$this->routes = $config;
	}
	
	/**
	 * Set the module name, when nothing is set, the default module will be set
	 * @param string $module
	 * @return void
	 */
	protected function setModuleName($module) {
		if($module == '')
			$module = $this->defaultModule;
			
		$this->request->setModuleName($module);
	}
	
	/**
	 * Set the controller name, when nothing is set, the default controller will be set
	 * @param string $controller
	 * @return void
	 */
	protected function setControllerName($controller) {
		if($controller == '')
			$controller = $this->defaultController;
			
		$this->request->setControllerName($controller);
	}
	
	/**
	 * Set the action name, when nothing is set, the default action will be set
	 * @param string $action
	 * @return void
	 */
	protected function setActionName($action) {
		if($action == '')
			$action = $this->defaultAction;
			
		$this->request->setActionName($action);
	}
	
	/**
	 * Check if the given param is a module
	 * @param string $param
	 * @return boolean
	 */
	protected function isModule($param) {
		$front = Controller\Frontcontroller::getInstance();
		$moduleArray = scandir($front->getModuleDirectory());
		return in_array($param,$moduleArray);
	}
	
	/**
	 * Check if the given param is a controller
	 * @param string $param
	 * @return boolean
	 */
	protected function isController($param) {
		$front = Controller\Frontcontroller::getInstance();
		
		if($front->getModuleName() == "")
			$front->setModuleName($this->defaultModule);
			
		$controllerArray = scandir($front->getControllerDirectory());
		$param = ucfirst($param) . 'Controller.php';
		return in_array($param,$controllerArray);
	}
	
	/**
	 * Check if the given param is an action
	 * @param string $param
	 * @return boolean
	 */
	protected function isAction($param) {
		$param = $param . 'Action';
		$front = Controller\Frontcontroller::getInstance();
		$methodsArray = get_class_methods(ucfirst($front->getControllerName()) . 'Controller');
		return in_array($param,$methodsArray);
	}
	
	/**
	 * Check if the given param is a route
	 * @param string $param
	 * @return boolean
	 */
	protected function isRoute($param) {
		return array_key_exists($param,$this->routes);
	}
	
	/**
	 * 
	 * Route the incoming request to the right module,controller,action.
	 * @param Application\Http\Request $request
	 * @param Application\Http\Response $response
	 * @throws Router\Exception
	 * @return void
	 */
	public function route(Application\Http\Request $request, Application\Http\Response $response) {
		$this->request = $request;
		
		if(!is_array($this->routes))
			throw new Router\Exception('Routes not loaded');
		
		if(isset($this->requestData[0]) && $this->isModule($this->requestData[0]) && $this->isRoute($this->requestData[0]))
			throw new Router\Exception('Controller and route has the same name.');

		// passende Route routen
		if(isset($this->requestData[0]) && $this->isRoute($this->requestData[0])) {
			$this->setModuleName($this->routes[$this->requestData[0]]['module']);
			$this->setControllerName($this->routes[$this->requestData[0]]['controller']);
			$this->setActionName($this->routes[$this->requestData[0]]['action']);
			// Get Params setzen
			for($g=1;$g<count($this->requestData);$g=$g+2) {
				if(!isset($this->requestData[$g+1]))
					$this->requestData[$g+1] = null;
				$this->request->setGet($this->requestData[$g],$this->requestData[$g+1]);
			}			
			
		// URL Eingabe routen /module[/controller/action/*]
		} else if(isset($this->requestData[0]) && $this->isModule($this->requestData[0])) {
			$this->setModuleName($this->requestData[0]);
			$this->setControllerName($this->requestData[1]);
			$this->setActionName($this->requestData[2]);
			// Get Params setzen
			for($g=3;$g<count($this->requestData);$g=$g+2) {
				if(!isset($this->requestData[$g+1]))
					$this->requestData[$g+1] = null;
				$this->request->setGet($this->requestData[$g],$this->requestData[$g+1]);
			}
			
		// URL Eingabe routen /controller[/action/*] (module = default)
		} else  if(isset($this->requestData[0]) && $this->isController($this->requestData[0])) {	
			$this->setModuleName($this->defaultModule);
			$this->setControllerName($this->requestData[0]);
			$this->setActionName($this->requestData[1]);	
			// Get Params setzen
			for($g=2;$g<count($this->requestData);$g=$g+2) {
				if(!isset($this->requestData[$g+1]))
					$this->requestData[$g+1] = null;
				$this->request->setGet($this->requestData[$g],$this->requestData[$g+1]);
			}
			
		// URL Eingabe routen /action/* (module = default, controller = default) -> sonst 404 !
		} else {
			$this->setModuleName($this->defaultModule);
			$this->setControllerName($this->defaultController);
			
			if(isset($this->requestData[0]) && $this->isAction($this->requestData[0])) {
				$this->setActionName($this->requestData[0]);
				// Get Params setzen
				for($g=1;$g<count($this->requestData);$g=$g+2) {
					if(!isset($this->requestData[$g+1]))
						$this->requestData[$g+1] = null;
					$this->request->setGet($this->requestData[$g],$this->requestData[$g+1]);
				}
				
			} else {
				throw new Router\Exception('No module, controller or route found.',404);
			}
			
		}

	}
	
	
}