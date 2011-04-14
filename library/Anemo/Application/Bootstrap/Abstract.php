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

namespace Anemo\Application\Bootstrap;


require_once 'Anemo/Exception.php';
require_once 'Anemo/Autoloader.php';

/**
 * Abstract bootstrap class is the head of the framework and loads and run the application
 * @abstract
 * @author vince
 * @version 1.0
 */
abstract class BootstrapAbstract
{
	public 		$config 	= array();
	protected 	$resources 	= array();
	protected	$runtime;
	
	protected 	$rootDir = "";
	
	protected 	$application = null;
	
	/**
	 * Init the environment, the autloader and load the config files
	 * @return void
	 */
	public function __construct() {
		$this->registerEnvironment();
		$this->registerAutoloader();
		$this->loadConfigs(ROOT.'/configs');
	}
	
	/**
	 * Return the runtime - the absolute time the script ran
	 * @return float
	 */
	public function getRuntime() {
		return $this->runtime;
	}

	/**
	 * Append a new resource
	 * @param string $resourceName
	 * @param string $resourceValue
	 * @return \Anemo\Bootstrap\BootstrapAbstract
	 */
	public function setResource($resourceName,$resourceValue) {
		$this->resources[strtolower($resourceName)] = $resourceValue;
		return $this;
	}
	
	/**
	 * Return the object of the given resoruce
	 * @param string $resourceName
	 * @return object
	 */
	public function getResource($resourceName) {
		if($this->bootstrap($resourceName))
			return $this->resources[strtolower($resourceName)];
	}
	
	/**
	 * Check if the gevien resource is inside the resource array
	 * @param string $resourceName
	 * @return boolean
	 */
	protected function hasResource($resourceName) {
		return isset($this->resources[strtolower($resourceName)]);
	}
	
	/**
	 * Return the frontcontroller object
	 * @return \Anemo\Controlle\Frontcontroller
	 */
	public function getFrontcontroller() {
		return $this->getResource('frontcontroller');
	}
	
	/**
	 * Set the debug settings for the environment
	 * @return void
	 */
	protected function registerEnvironment() {
		if(APPLICATION_ENV == 'development') {
			ini_set('error_reporting', E_ALL);
			ini_set('display_errors', 1);
		} else {
			ini_set('display_errors', 0);
		}
	}
	
	/**
	 * Start and register a new autoloader
	 * @return void
	 */
	protected function registerAutoloader() {
		$loader = new \Anemo\Autoloader();
		$loader->register();
	}
	
	/**
	 * Load automatically all config files and save them into the config array
	 * @param string $configDir
	 * @return void
	 */
	protected function loadConfigs($configDir) {
		$configs = scandir($configDir);
		
		foreach($configs as $c) {
			if(is_dir($c))
				continue;
				
			$config = new \Anemo\Config($c,$configDir);
			$this->config[$config->getFilename()] = $config->toArray();
		}
	}
	
	/**
	 * If no argument is given, all resources will be load and started. With a given resource, only the specified resource will be boostrapped.
	 * @param string $resource
	 * @return void
	 */
	public function bootstrap($resource = null) {
		$applicationClass = get_class($this);
		
		if($resource === null) { // alles "bootstrappen"
			$classMethods = get_class_methods($applicationClass);
			foreach($classMethods as $method) {
				if(preg_match("/init.*/",$method) == 1) {
					// check ob die Resource schon gebootstrapped wurde
					$resource = str_replace('init','',ucfirst(strtolower($method)));
					if($this->hasResource($resource))
						continue;
					
					$resource = call_user_func(array($applicationClass,$method));
					$this->setResource(str_replace('init','',strtolower($method)),$resource);
				}
			}

		} else { // einzelne resource "bootstrappen"
			if($this->hasResource($resource))
				return true;
			
			$resourceMethod = 'init' . ucfirst(strtolower($resource));
			if(method_exists($this,$resourceMethod)) {
				$this->setResource($resource,call_user_func(array($applicationClass,$resourceMethod)));
			} else {
				throw new Exception('Resource ' . $resource . ' not found.');
			}
		}
		
		return true;
	}
	
	/**
	 * Start and run the application
	 * @param \Anemo\Application\Http\Response $response
	 * @return void
	 */
	public function run(\Anemo\Application\Http\Response $response = null){
		
		$starttime = microtime(true);
		
		if($response == null) {
			$this->setResource('frontcontroller',\Anemo\Controller\Frontcontroller::getInstance());
			$this->getFrontcontroller()->init($this->config);
			
			$this->setResource('layout',\Anemo\Layout::getInstance());
			$this->getResource('layout')->init($this->getResource('view'));
			
			$this->bootstrap();		
			
			\Anemo\Registry::set('bootstrap',$this);
			
			while( ($response = $this->getFrontcontroller()->dispatch()) != $response) {}
		}
		
		$response->send();
		
		$endtime = microtime(true);
		$this->runtime = $endtime - $starttime;
	}
	
	
}