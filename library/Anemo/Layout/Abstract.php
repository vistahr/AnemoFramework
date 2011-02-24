<?php
namespace Anemo\Layout;

abstract class LayoutAbstract
{
	
	protected static $instance = null;
	
	protected $layoutPath 	= "";
	protected $view 		= null;
	
	protected $content		= "";
	
	protected $disableLayout= false;
	
	
	protected $headTitle 	= "";
	protected $headMeta 	= array();
	protected $headStyle	= array();
	protected $headScript	= array();
	
	
	private function __construct() {}
	private function __clone() {}
	
	
	public function init($view) {
		$this->view	= $view;
	}
	
	public function __call($name,$args) {
		throw new Exception('Method ' . $name . ' does not exists.');
	}	
	
	public static function getInstance(){
		if(self::$instance === null){
	    	self::$instance = new \Anemo\Layout();
	    }
	    return self::$instance;
	}
		
	public function getPublicDirectory() {
		return $this->getFrontcontroller()->getBasePath();
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	public function getContent() {
		return $this->content;
	}
	

	public function setLayout($layoutPath) {
		$this->layoutPath 	= $layoutPath;
	}
	public function getLayout() {
		return $this->layoutPath;
	}
	
	
	public function getResponse() {
		
		if(!$this->isLayoutDisabled()) {
			$this->view->assign('layout',$this);
			$layout = $this->view->fetch($this->getLayout());
		} else {
			$layout = $this->getContent();
		}
		
		return $layout;
	}
	
	public function __toString() {
		return $this->getResponse();
	}
	
	protected function getFrontcontroller() {
		return \Anemo\Controller\Frontcontroller::getInstance();
	}
	

}