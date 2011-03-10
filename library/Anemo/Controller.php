<?php
namespace Anemo;


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

