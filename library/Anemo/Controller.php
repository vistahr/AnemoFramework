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
	
	protected function send($result,$type) {
		$this->disableTemplate();
		$this->getLayout()->disableLayout();
		$this->getResponse()->addHeader('Cache-Control','no-cache, must-revalidate')
							->addHeader('Expires','Mon, 26 Jul 1997 05:00:00 GMT')
							->setContentType($type);
		return $result;
	}
	
	protected function sendAjax(array $result) {
		return json_encode($this->send($result,'application/json'));
	}
	
	protected function sendXML($result) {
		$xml = simplexml_load_string($this->send($result,'text/xml'));
		return $xml->asXML();
	}	
	
	protected function loadTemplate($template) {
		$this->template = $template;
	}
	
	
	public function baseUrl($url = "") {
		return $this->getFrontcontroller()->getBasePath() . '/' . $url;
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
		
	
	public function forwardAndExit($module,$controller,$action,$param = array()) {
		$this->getRequest()->setModuleName($module)
						   ->setControllerName($controller)
						   ->setActionName($action)
						   ->setParams($param);	
		$response = $this->getFrontcontroller()->execute();
		//$response = $this->executeAction($action);
		$this->disableTemplate(); 
		return $response;
	}
	
	public function getID() {
		return \Anemo\ID::getInstance();
	}

}

