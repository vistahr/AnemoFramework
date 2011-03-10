<?php
namespace Anemo\Application\Bootstrap;

use Anemo\Application;

class Module extends BootstrapAbstract
{
	
	public $config 		= array();
	protected $layout 	= null;
	
	protected $request  = null;
	
	
	public function __construct() {
		$this->loadConfigs($this->getFrontcontroller()->getModuleDirectory() . '/' . $this->getFrontcontroller()->getModuleName() . '/configs');
		$this->layout = $this->getFrontcontroller()->getResource('layout');
		
		$this->request = $this->getFrontcontroller()->getRequest();
		
		if(!$this->layout instanceof \Anemo\Layout)
			throw new Application\Exception('Layout not defined');
		
		$this->initTitles();
		$this->initScripts();
		$this->initStyles();
		$this->initMetas();
	}
	
	
	public function getFrontcontroller() {
		return \Anemo\Controller\Frontcontroller::getInstance();
	}
	
	protected function getLayout() {
		return $this->getFrontcontroller()->getResource('layout');
	}
	
	
	protected function initTitles() {
		$title 	 = "";
		
		if(isset($this->config['titles']['title']))
		 	$title = $this->config['titles']['title'];

		if(isset($this->config['titles'][$this->request->getControllerName()]['title']))
		 	$title .= $this->config['titles']['seperator'] . $this->config['titles'][$this->request->getControllerName()]['title'];
		 	
		if(isset($this->config['titles'][$this->request->getControllerName()]['action'][$this->request->getActionName()]))
		 	$title .= $this->config['titles']['seperator'] . $this->config['titles'][$this->request->getControllerName()]['action'][$this->request->getActionName()];
		 	
		$this->layout->setTitle($title);
	}
	
	
	protected function initScripts() {
		$scripts = array();
		
		if(isset($this->config['scripts']['script'])) {
			foreach($this->config['scripts']['script'] as $moduleScripts) {
				$scripts[] = $this->layout->baseUrl('scripts/' . $moduleScripts);
			}
		}
		
		if(isset($this->config['scripts'][$this->request->getControllerName()])) {
			foreach($this->config['scripts'][$this->request->getControllerName()] as $action => $internalScripts) {
				
				if(is_array($internalScripts) && $action == 'script') {
					foreach($internalScripts as $sc) {
						if(preg_match("/http:/i", $sc)) {
							$scripts[] = $sc;
						} else {
							$scripts[] = $this->layout->baseUrl('scripts/' . $sc);
						}
					}
					
				} else if(is_array($internalScripts) && $action == $this->request->getActionName()) {
					foreach($internalScripts as $sc) {
						if(preg_match("/http:/i", $sc)) {
							$scripts[] = $sc;
						} else {
							$scripts[] = $this->layout->baseUrl('scripts/' . $sc);
						}
					}
				}
			}
		}
		
		$this->layout->setScript($scripts);
	}

	
	protected function initStyles() {
		$styles = array();
		
		if(isset($this->config['styles']['style'])) {
			foreach($this->config['styles']['style'] as $moduleStyle) {
				$styles[] = $this->layout->baseUrl('layout/' . $moduleStyle);
			}
		}
		
		if(isset($this->config['styles'][$this->request->getControllerName()])) {
			foreach($this->config['styles'][$this->request->getControllerName()] as $action => $internalStyle) {
				
				if(is_array($internalStyle) && $action == 'style') {
					foreach($internalStyle as $sc) {
						if(preg_match("/http:/i", $sc)) {
							$styles[] = $sc;
						} else {
							$styles[] = $this->layout->baseUrl('layout/' . $sc);
						}
					}
					
				} else if(is_array($internalStyle) && $action == $this->request->getActionName()) {
					foreach($internalStyle as $sc) {
						if(preg_match("/http:/i", $sc)) {
							$styles[] = $sc;
						} else {
							$styles[] = $this->layout->baseUrl('layout/' . $sc);
						}
					}
				}
			}
		}
		
		$this->layout->setStyle($styles);
	}
	
	
	protected function initMetas() {
		$metas = array();
		
		if(isset($this->config['metas']['meta']) && is_array($this->config['metas']['meta'])) {
			foreach($this->config['metas']['meta'] as $mk => $mv) {
				$metas[$mk] = $mv;
			}
		}
		
		if(isset($this->config['metas'][$this->request->getControllerName()])) {
			foreach($this->config['metas'][$this->request->getControllerName()] as $action=>$meta) {
				if(is_array($meta) && $action == $this->request->getActionName()) {
					foreach($meta as $mk => $mv) {
						if(isset($metas[$mk])) {
							$metas[$mk] = $metas[$mk] . ', ' . $mv; // appending when exists
						} else {
							$metas[] = $mv; //add
						}
					}
				}
			}
		}
		
		$this->layout->setMeta($metas);
	}
	
	
}

