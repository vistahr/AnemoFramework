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

namespace Anemo\Layout;

/**
 * Layout class with the basic layout-functions
 * @abstract
 * @author vince
 * @version 1.0
 */
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
	
	/**
	 * The init set the view object
	 * @param object $view
	 * @return void
	 */
	public function init($view) {
		$this->view	= $view;
	}
	
	/**
	 * If a called function does not exists, this function is called
	 * @param string $name
	 * @param array $args
	 * @throws Exception
	 * @return void
	 */
	public function __call($name,$args) {
		throw new Exception('Method ' . $name . ' does not exists.');
	}	
	
	/**
	 * Return the self instance of this singleton
	 * @return \Anemo\Layout
	 */
	public static function getInstance(){
		if(self::$instance === null){
	    	self::$instance = new \Anemo\Layout();
	    }
	    return self::$instance;
	}
	
	/**
	 * A mapper for getting a assigned view var
	 * @param string $key
	 * @return string
	 */
	public function __get($key) {
		//var_dump($this->view->getVariable($key)->value);
		return $this->view->getVariable($key)->value;
	}
	
	/**
	 * A mapper for assigning a key-value pair to the view object
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function assign($key, $value) {
		$this->view->assign($key, $value);
	}
	
	/**
	 * Return the public directory (the base path)
	 * @return string
	 */
	public function getPublicDirectory() {
		return $this->getFrontcontroller()->getBasePath();
	}
	
	/**
	 * Set the layout content
	 * @param string $content
	 * @return void
	 */
	public function setContent($content) {
		$this->content = $content;
	}
	
	/**
	 * Return the layout content
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}
	
	/**
	 * Set the layout path
	 * @param string $layoutPath
	 * @return void
	 */
	public function setLayout($layoutPath) {
		$this->layoutPath 	= $layoutPath;
	}
	
	/**
	 * Return the layout path
	 * @return string
	 */
	public function getLayout() {
		return $this->layoutPath;
	}
	
	/**
	 * Check if layout is enabled, get response, fill it, if enabled, in the layout and return the response
	 * @return string
	 */
	public function getResponse() {
		
		if(!$this->isLayoutDisabled()) {
			$this->view->assign('layout',$this);
			$layout = $this->view->fetch($this->getLayout());
		} else {
			$layout = $this->getContent();
		}
		
		return $layout;
	}
	
	/**
	 * Return the response. Wrapper for getResponse.
	 * @return string
	 */
	public function __toString() {
		return $this->getResponse();
	}
	
	/**
	 * Return the frontcontroller instance
	 * @return \Anemo\Controller\Frontcontroller
	 */
	protected function getFrontcontroller() {
		return \Anemo\Controller\Frontcontroller::getInstance();
	}
	

}