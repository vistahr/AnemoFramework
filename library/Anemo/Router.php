<?php
namespace Anemo;


class Router 
{
	protected $routes = array();
	protected $requestData = array();
	

	protected $defaultController;
	protected $defaultAction;
	protected $defaultModule;
	
	protected $request;
	
	public function __construct() {
		$this->requestData = Controller\Router\Rewriter::rewriteRequest();
	}
	
	
	public function setDefaultModule($module) {
		$this->defaultModule = $module;
		return $this;
	}
	public function setDefaultController($controller) {
		$this->defaultController = $controller;
		return $this;
	}
	public function setDefaultAction($action) {
		$this->defaultAction = $action;
		return $this;
	}
	
	
	public function loadRoutes($config) {
		$this->setDefaultModule($config['']['module'])
			 ->setDefaultController($config['']['controller'])
			 ->setDefaultAction($config['']['action']);
		
		$this->routes = $config;
	}
	
	
	protected function setModuleName($module) {
		if($module == '')
			$module = $this->defaultModule;
			
		$this->request->setModuleName($module);
	}
	protected function setControllerName($controller) {
		if($controller == '')
			$controller = $this->defaultController;
			
		$this->request->setControllerName($controller);
	}
	protected function setActionName($action) {
		if($action == '')
			$action = $this->defaultAction;
			
		$this->request->setActionName($action);
	}
	
	
	protected function isModule($param) {
		$front = Controller\Frontcontroller::getInstance();
		$moduleArray = scandir($front->getModuleDirectory());
		return in_array($param,$moduleArray);
	}
	
	protected function isController($param) {
		$front = Controller\Frontcontroller::getInstance();
		
		if($front->getModuleName() == "")
			$front->setModuleName($this->defaultModule);
			
		$controllerArray = scandir($front->getControllerDirectory());
		$param = ucfirst($param) . 'Controller.php';
		return in_array($param,$controllerArray);
	}
	
	protected function isAction($param) {
		$param = $param . 'Action';
		$front = Controller\Frontcontroller::getInstance();
		$methodsArray = get_class_methods(ucfirst($front->getControllerName()) . 'Controller');
		return in_array($param,$methodsArray);
	}
	
	protected function isRoute($param) {
		return array_key_exists($param,$this->routes);
	}
	
	
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
		
		// URL Eingabe routen /module[/controller/action/*]
		} else if(isset($this->requestData[0]) && $this->isModule($this->requestData[0])) {
			$this->setModuleName($this->requestData[0]);
			$this->setControllerName($this->requestData[1]);
			$this->setActionName($this->requestData[2]);
						
		// URL Eingabe routen /controller[/action/*] (module = default)
		} else  if(isset($this->requestData[0]) && $this->isController($this->requestData[0])) {	
			$this->setModuleName($this->defaultModule);
			$this->setControllerName($this->requestData[0]);
			$this->setActionName($this->requestData[1]);	
		
		// URL Eingabe routen /action/* (module = default, controller = default) -> sonst 404 !
		} else {
			$this->setModuleName($this->defaultModule);
			$this->setControllerName($this->defaultController);
			
			if(isset($this->requestData[0]) && $this->isAction($this->requestData[0])) {
				$this->setActionName($this->requestData[0]);
				
			} else {
				throw new Router\Exception('No module, controller or route found.',404);
			}
			
		}
		
		
		
	}
	
	
}