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
	
	
	/**
	 * Create a new controller and if exists, it calls the init function of the parent class
	 * @throws \Anemo\Exception
	 * @return void
	 */
	public function __construct() {
		$this->froncontroller = Frontcontroller::getInstance();
		
		$this->getView()->assign('view',$this); // TODO
		
		if(method_exists($this,'init'))
			call_user_func(array($this,'init'));
	}
	
	/**
	 * Function is called, if a method does not exist
	 * @param string $name
	 * @param mixed $args
	 * @throws Exception
	 * @return void
	 */
	public function __call($name,$args) {
		throw new Exception('Method ' . $name . ' does not exist');
	}
	
	/**
	 * Return the bootstrap object
	 * @return \Anemo\Application\Bootstrap\BootstrapAbstract
	 */
	public function getBootstrap() {
		if($this->bootstrap == null)
			$this->bootstrap = \Anemo\Registry::get('bootstrap');
		return $this->bootstrap;
	}
	
	/**
	 * Return a specified resource
	 * @param string $resource
	 * @return object
	 */
	public function getResource($resource) {
		return $this->getBootstrap()->getResource($resource);
	}
	
	/**
	 * Return the frontcontroller
	 * @return \Anemo\Controller\Frontcontroller
	 */
	public function getFrontcontroller() {
		return $this->getResource('frontcontroller');
	}
	
	/**
	 * Return the view resource, which was set in the bootstrap
	 * @return object
	 */
	public function getView() {
		return $this->getResource('view');
	}
	
	/**
	 * Return the layout object
	 * @return \Anemo\Layout
	 */
	public function getLayout() {
		return $this->getResource('layout');
	}
	
	/**
	 * Return the request object
	 * @return \Anemo\Application\Http\Request
	 */
	public function getRequest() {
		return $this->getFrontcontroller()->getRequest();
	}
	
	/**
	 * Return the response object
	 * @return \Anemo\Application\Http\Response
	 */
	public function getResponse() {
		return $this->getFrontcontroller()->getResponse();
	}
	
	/**
	 * Enable the template output.
	 * @return void
	 */
	public function enableTemplate() {
		if($this->isTemplateDisabled())
			$this->disableTemplate = false;
	}
	
	/**
	 * disable the template output
	 * @return void
	 */
	public function disableTemplate() {
		if(!$this->isTemplateDisabled())
			$this->disableTemplate = true;
	}
	
	/**
	 * Return the state of the template output
	 * @return boolean
	 */
	public function isTemplateDisabled() {
		return $this->disableTemplate;
	}
	
	/**
	 * Execute the given action and return the response
	 * @param string $actionName
	 * @throws Exception
	 * @return string 
	 */
	public function executeAction($actionName) {
		$actionMethodName = $actionName . 'Action';
		
		if(!method_exists($this,$actionMethodName)) 
			throw new Exception('Action ' . $actionMethodName . ' does not exist');
		
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

	/**
	 * Return the specified part of a camelCase string
	 * @param string $camelCaseInput
	 * @param int $arrayIndex
	 * @return string
	 */
	private function getCamelCasePart($camelCaseInput, $arrayIndex = 0) {
		$camelCaseOutputArray = preg_split("{(?<=[a-z]) (?=[A-Z])}x", $camelCaseInput);
		return $camelCaseOutputArray[$arrayIndex];
	}
	
}

