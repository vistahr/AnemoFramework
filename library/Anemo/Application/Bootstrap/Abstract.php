<?php
namespace Anemo\Application\Bootstrap;


require_once 'Anemo/Exception.php';
require_once 'Anemo/Autoloader.php';

abstract class BootstrapAbstract
{
	public 		$config 	= array();
	protected 	$resources 	= array();
	protected	$runtime;
	
	protected 	$rootDir = "";
	
	protected 	$application = null;
	
	
	public function __construct() {
		$this->registerEnvironment();
		$this->registerAutoloader();
		$this->loadConfigs(ROOT.'/configs');
	}

	public function getRuntime() {
		return $this->runtime;
	}
		
	public function setResource($resourceName,$resourceValue) {
		$this->resources[strtolower($resourceName)] = $resourceValue;
		return $this;
	}
	public function getResource($resource) {
		if($this->bootstrap($resource))
			return $this->resources[strtolower($resource)];
	}
	protected function hasResource($resource) {
		return isset($this->resources[strtolower($resource)]);
	}

	public function getFrontcontroller() {
		return $this->getResource('frontcontroller');
	}
	
	protected function registerEnvironment() {
		if(APPLICATION_ENV == 'development') {
			ini_set('error_reporting', E_ALL);
			ini_set('display_errors', 1);
		} else {
			ini_set('display_errors', 0);
		}
	}
	
	protected function registerAutoloader() {
		$loader = new \Anemo\Autoloader();
		$loader->register();
	}
	

	protected function loadConfigs($configDir) {
		$configs = scandir($configDir);
		
		foreach($configs as $c) {
			if(is_dir($c))
				continue;
				
			$config = new \Anemo\Config($c,$configDir);
			$this->config[$config->getFilename()] = $config->toArray();
		}
	}
	
	
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