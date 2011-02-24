<?php
namespace Anemo\Controller\Router;

use Anemo\Controller;

class Rewriter
{
	protected $request = null;
	
	
	public static function getRequest() {
		$front = Controller\Frontcontroller::getInstance();
		return $front->getRequest();
	}
	
	public static function rewriteRequest() {
		$front 		= Controller\Frontcontroller::getInstance();
		$urlParams 	= preg_replace('#'.$front->getBasePath().'#','',Rewriter::getRequest()->getServer('REQUEST_URI'),1);
		
		if(preg_match('#.*\.php#',$urlParams))
			$urlParams = preg_replace('#.*\.php#','',$urlParams);
			
		$urlParamsArray = explode('/',$urlParams);
		
		$filteredUrlParamsArray = array();
		for($i=0;$i<count($urlParamsArray);$i++) {
			if($urlParamsArray[$i] != '')
				$filteredUrlParamsArray[] = $urlParamsArray[$i];
		}
		
		// mindestens 3 Werte "/module/controller/action", sonst mit leer ("") auffüllen
		if(count($filteredUrlParamsArray)<3)
			$filteredUrlParamsArray = array_merge($filteredUrlParamsArray,array_fill(0,(3-count($filteredUrlParamsArray)),''));
		
		return $filteredUrlParamsArray;
	}
	
	
	
}
	