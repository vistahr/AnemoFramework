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

namespace Anemo\Controller\Router;

use Anemo\Controller;

class Rewriter
{
	protected $request = null;
	
	
	public static function getRequest() {
		$front = Controller\Frontcontroller::getInstance();
		return $front->getRequest();
	}
	
	
	public static function toCamelCase($input) {
		if($parts = explode('-', $input)) {
			$parts    = $parts ? array_map('ucfirst', $parts) : array($input);
		    $parts[0] = lcfirst($parts[0]);
		    $input 	  = implode('', $parts);
		    return $input;
		}
		return $input;
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
				$filteredUrlParamsArray[] = Rewriter::toCamelCase($urlParamsArray[$i]);
		}
		
		// mindestens 3 Werte "/module/controller/action", sonst mit leer ("") auffüllen
		if(count($filteredUrlParamsArray)<3)
			$filteredUrlParamsArray = array_merge($filteredUrlParamsArray,array_fill(0,(3-count($filteredUrlParamsArray)),''));
		
		return $filteredUrlParamsArray;
	}
	

}
	