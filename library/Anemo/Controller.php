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
 * Controller class is the head of all controllers
 * @author vince
 * @version 1.0
 */
class Controller extends Controller\ControllerAbstract
{
	/**
	 * Send a result with the given type
	 * @param string $result
	 * @param string $type
	 * @return string $result
	 */
	protected function send($result,$type) {
		$this->disableTemplate();
		$this->getLayout()->disableLayout();
		$this->getResponse()->addHeader('Cache-Control','no-cache, must-revalidate')
							->addHeader('Expires','Mon, 26 Jul 1997 05:00:00 GMT')
							->setContentType($type);
		return $result;
	}
	
	/**
	 * Send a ajax result
	 * @param array $result
	 * @return string
	 */
	protected function sendAjax(array $result) {
		return json_encode($this->send($result,'application/json'));
	}
	
	/**
	 * Send a XML result
	 * @param string $result
	 * @return string
	 */
	protected function sendXML($result) {
		$xml = simplexml_load_string($this->send($result,'text/xml'));
		return $xml->asXML();
	}	
	
	/**
	 * Load another template
	 * @param string $template
	 * @return void
	 */
	protected function loadTemplate($template) {
		$this->template = $template;
	}
	
	/**
	 * Return the base url path
	 * @param string $url
	 * @return string
	 */
	public function baseUrl($url = "") {
		return $this->getFrontcontroller()->getBasePath() . '/' . $url;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Anemo\Controller.ControllerAbstract::getFrontcontroller()
	 */
	public function getFrontcontroller() {
		return $this->getResource('frontcontroller');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Anemo\Controller.ControllerAbstract::getView()
	 */
	public function getView() {
		return $this->getResource('view');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Anemo\Controller.ControllerAbstract::getLayout()
	 */
	public function getLayout() {
		return $this->getResource('layout');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Anemo\Controller.ControllerAbstract::getRequest()
	 */
	public function getRequest() {
		return $this->getFrontcontroller()->getRequest();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Anemo\Controller.ControllerAbstract::getResponse()
	 */
	public function getResponse() {
		return $this->getFrontcontroller()->getResponse();
	}
		
	/**
	 * Forward the request, disable the current template
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 * @return string $response
	 */
	public function forwardAndExit($module,$controller,$action,$params = array()) {
		$this->getRequest()->setModuleName($module)
						   ->setControllerName($controller)
						   ->setActionName($action)
						   ->setParams($params);	
		$response = $this->getFrontcontroller()->execute();
		$this->disableTemplate(); 
		return $response;
	}
	
	/**
	 * Redirect to the given module,controller, action with the params. 
	 * The params are saved temporary in the session.
	 * If module or controller are equal false, it wont be considered in the url
	 * @param mixed $module
	 * @param mixed $controller
	 * @param string $action
	 * @param array $params
	 */
	public function redirect($module,$controller,$action,$params = array()) {
		$this->getRequest()->setModuleName($module)
						   ->setControllerName($controller)
						   ->setActionName($action)
						   ->paramsToSession($params);		   				   
		header("Location: " . $this->baseUrl() . $this->getRequest()->getUrl($module,$controller));
		exit();
	}
	
	/**
	 * Returns the instance
	 */
	public function getID() {
		return \Anemo\ID::getInstance();
	}

}

