<?php
namespace Anemo\Controller;

abstract class ControllerAbstract
{
	
	protected $bootstrap 		= null;
	protected $frontcontroller	= null;
	
	protected $disableTemplate 	= false;
	protected $template 		= "";
	
	
	
	public function __construct() {
		$this->froncontroller = Frontcontroller::getInstance();
		
		// ModuleBootstrap
		$moduleBootstrap = $moduleConfigDir = $this->froncontroller->getModuleDirectory() . '/' . $this->froncontroller->getModuleName() . '/Bootstrap.php';
		if(!@include_once $moduleBootstrap)
			throw new \Anemo\Exception('Modulebootstrap ' . $moduleBootstrap . ' not found');
			
		$moduleBootstrapClass = $this->froncontroller->getModuleName() . 'Bootstrap';
		$moduleBootstrap = new $moduleBootstrapClass();
		
		$this->getView()->assign('view',$this); // TODO
		
		if(method_exists($this,'init'))
			call_user_func(array($this,'init'));
	}
	
	public function __call($name,$args) {
		throw new Exception('Method ' . $name . ' does not exists.');
	}
		
	public function getBootstrap() {
		if($this->bootstrap == null)
			$this->bootstrap = \Anemo\Registry::get('bootstrap');
		return $this->bootstrap;
	}
	
	public function getResource($resource) {
		return $this->getBootstrap()->getResource($resource);
	}
	public function getFrontcontroller() {
		return $this->getResource('frontcontroller');
	}
	public function getView() {
		return $this->getResource('view');
	}
	public function getLayout() {
		return $this->getResource('layout');
	}
	
	public function getRequest() {
		return $this->getFrontcontroller()->getRequest();
	}
	public function getResponse() {
		return $this->getFrontcontroller()->getResponse();
	}
	
	public function enableTemplate() {
		if($this->isTemplateDisabled())
			$this->disableTemplate = false;
	}
	public function disableTemplate() {
		if(!$this->isTemplateDisabled())
			$this->disableTemplate = true;
	}
	public function isTemplateDisabled() {
		return $this->disableTemplate;
	}
	
	public function executeAction($actionName) {
		$actionMethodName = $actionName . 'Action';
		
		if(!method_exists($this,$actionMethodName)) 
			throw new Exception('Action ' . $actionMethodName . ' does not exists');
			
		$methodOutput = call_user_func(array($this,$actionMethodName));
		
		if($this->isTemplateDisabled())
			return $methodOutput;
		
		$template = $this->template;
		$this->template = ""; // zurücksetzen
		
		if($template == "")
			$template = $this->getCamelCasePart($actionName);
		
		$response =  $this->getView()->fetch($this->getRequest()->getModuleName() . '/templates/' . $this->getRequest()->getControllerName() . '/'. $template . '.tpl');
		
		return $response;
	}

	
	private function getCamelCasePart($camelCaseInput, $arrayIndex = 0) {
		$camelCaseOutputArray = preg_split("{(?<=[a-z]) (?=[A-Z])}x", $camelCaseInput);
		return $camelCaseOutputArray[$arrayIndex];
	}
	
}

