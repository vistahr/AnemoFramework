<?php
namespace Anemo;

class Layout extends Layout\LayoutAbstract
{
	
	public function disableLayout() {
		if(!$this->isLayoutDisabled())
			$this->disableLayout = true;
	}
	
	public function isLayoutDisabled() {
		return $this->disableLayout;
	}
	
	
	public function executeAction($module,$controller,$action) {
		$front = Controller\Frontcontroller::getInstance();
		$front->setModuleName($module)->setControllerName($controller)->setActionName($action);
		return $front->execute();
	}
	
	public function baseUrl($url = "") {
		return $this->getPublicDirectory() . '/' . $url;
	}
	
	public function getRequest() {
		return $this->getFrontcontroller()->getRequest();
	}
	
	
	public function setTitle($title) {
		$this->headTitle = $title;
	}
	public function headTitle() {
		return '<title>' .  $this->headTitle . '</title>';
	}
	
	public function setScript(array $script) {
		$this->headScript = $script;
	}
	public function headScript() {
		$headScript = "";
		foreach($this->headScript as $script) {
			$headScript .= "<script src=\"" . $script . "\" type=\"text/javascript\"></script> \n\t\t";
		}
		return $headScript;
	}
	
	public function setStyle($stlye) {
		$this->headStyle = $stlye;
	}
	public function headStyle() {
		$headStyle = "";
		foreach($this->headStyle as $style) {
			$headStyle .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"" . $style . "\"> \n\t\t";
		}
		return $headStyle;
	}

	public function setMeta($meta) {
		$this->headMeta = $meta;
	}
	public function headMeta() {
		$headMeta = "";
		foreach($this->headMeta as $metaName => $metaValue) {
			if(strtolower($metaName) == "content-type" || strtolower($metaName) == "refresh" || strtolower($metaName) == "content-language") {
				$headMeta .= "<meta http-equiv=\"" . $metaName . "\" content=\"" . $metaValue . "\"> \n\t\t";
			} else {
				$headMeta .= "<meta name=\"" . $metaName . "\" content=\"" . $metaValue . "\"> \n\t\t";
			}
		}
		return $headMeta;
	}
	

	
	
	
	
	
	
	
	public function appenHeadMeta() {
		// TODO 
	}
	public function prepandHeadMeta() {
		// TODO 
	}
	
	
	public function appenHeadTitle() {
		// TODO 
	}
	public function prepandHeadTitle() {
		// TODO 
	}
	
	
	public function appenHeadStyle() {
		// TODO 
	}
	public function prepandHeadStyle() {
		// TODO 
	}
	
	
	public function appenHeadScript() {
		// TODO 
	}
	public function prepandHeadScript() {
		// TODO 
	}
	

}