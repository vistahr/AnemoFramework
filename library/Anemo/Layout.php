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
 * The layoutclass handles the layout
 * @author vince
 * @version 1.0
 */
class Layout extends Layout\LayoutAbstract
{
	
	/**
	 * Disable the current layout
	 * @return void
	 */
	public function disableLayout() {
		if(!$this->isLayoutDisabled())
			$this->disableLayout = true;
	}
	
	/**
	 * Check if the current layout is disabled
	 * @return void
	 */
	public function isLayoutDisabled() {
		return $this->disableLayout;
	}
	
	/**
	 * Execute the given action
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @return string
	 */
	public function executeAction($module,$controller,$action) {
		$front = Controller\Frontcontroller::getInstance();
		$front->setModuleName($module)->setControllerName($controller)->setActionName($action);
		return $front->execute();
	}
	
	/**
	 * Return the base url
	 * @param string $url
	 * @return string
	 */
	public function baseUrl($url = "") {
		return $this->getPublicDirectory() . '/' . $url;
	}
	
	/**
	 * Return the frontcontroller request object
	 */
	public function getRequest() {
		return $this->getFrontcontroller()->getRequest();
	}
	
	/**
	 * Set the page title
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->headTitle = $title;
	}
	
	/**
	 * Return the head title
	 * @returnvoid
	 */
	public function headTitle() {
		return '<title>' .  $this->headTitle . '</title>';
	}
	
	/**
	 * Set the head script
	 * @param array $script
	 * @return void
	 */
	public function setScript(array $script) {
		$this->headScript = $script;
	}
	
	/**
	 * Return the head scripts
	 * @return string
	 */
	public function headScript() {
		$headScript = "";
		foreach($this->headScript as $script) {
			$headScript .= "<script src=\"" . $script . "\" type=\"text/javascript\"></script> \n\t\t";
		}
		return $headScript;
	}
	
	/**
	 * Set the head style
	 * @param array $stlye
	 * @return void
	 */
	public function setStyle(array $stlye) {
		$this->headStyle = $stlye;
	}
	
	/**
	 * Return the head style
	 * @return string
	 */
	public function headStyle() {
		$headStyle = "";
		foreach($this->headStyle as $style) {
			$headStyle .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"" . $style . "\" /> \n\t\t";
		}
		return $headStyle;
	}
	
	/**
	 * Set the head meta
	 * @param array $meta
	 */
	public function setMeta(array $meta) {
		$this->headMeta = $meta;
	}
	
	/**
	 * Return the head meta
	 * @return void
	 */
	public function headMeta() {
		$headMeta = "";
		foreach($this->headMeta as $metaName => $metaValue) {
			if(strtolower($metaName) == "content-type" || strtolower($metaName) == "refresh" || strtolower($metaName) == "content-language") {
				$headMeta .= "<meta http-equiv=\"" . $metaName . "\" content=\"" . $metaValue . "\" /> \n\t\t";
			} else {
				$headMeta .= "<meta name=\"" . $metaName . "\" content=\"" . $metaValue . "\" /> \n\t\t";
			}
		}
		return $headMeta;
	}
	
	/**
	 * Return the ID instance
	 */
	public function getID() {
		return \Anemo\ID::getInstance();
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