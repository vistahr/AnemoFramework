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

/**
 * The abstract controller body class holds the basic functions for the controller class
 * @abstract
 * @author vince
 * @version 1.0
 */
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

