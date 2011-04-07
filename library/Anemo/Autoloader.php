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

require_once 'Anemo/Autoloader/Exception.php';

/**
 * Autoloader class loads dynamically different libraryclasses
 * @author vince
 * @version 1.0
 */
class Autoloader
{
	
	public $loadedClasses = array();
	
	
	public function register() {
		spl_autoload_register(array(__CLASS__,'autoload'),true);
	}
	
	
    public function fileExists($classPath) {
   	 	$includePaths = explode(PATH_SEPARATOR, get_include_path());
    	foreach($includePaths as $ip) {
    		if(file_exists($ip.$classPath) && !is_dir($ip.$classPath))
				return $classPath;
    	}
    	return false;
    }

    
	public function autoload($className) {
		if(($class = $this->anemoLibraryAutoloader($className)) !== false || 
			($class = $this->anemoThirdpartyAutoloader($className)) !== false || 
			($class = $this->anemoControllerAutoloader($className)) !== false) {
			$this->loadedClasses[]	= $class;
			require_once $class;
		} else {
			//throw new Autoloader\Exception('File '. $className . '.php not found');
		}
        
        return true;
    }
    
    
	protected function anemoThirdpartyAutoloader($className) {
		$count = 0;
		$className = str_replace('\\', '/', $className, $count);
		if($count == 0) {
			return $this->fileExists('Thirdparty/' . $className . '/' . $className . '.php');
		} else {
			return $this->fileExists('Thirdparty/' . $className . '.php');
		}
		
	}
	
    
    protected function anemoLibraryAutoloader($className) {
    	if(preg_match("#(.+)\W[\w]+Abstract#",$className,$match))
    		$className = $match[1] . '\Abstract';
    		
		if(preg_match("#(.+)\W[\w]+Interface#",$className,$match))
    		$className = $match[1] . '\Interface';
    		
    	$classPath = str_replace('\\', '/', $className) . '.php';
    	//echo $classPath."<br/>";
    	return $this->fileExists($classPath);
    }
    
    
    protected function anemoControllerAutoloader($className) {
    	$front = Controller\Frontcontroller::getInstance();
    	
    	if(!isset($front))
    		throw new Loader\Exception('Frontcontroller not bootstrapped');
		
    	$classPath = $front->getModuleDirectory() . $front->getModuleName() . '/controllers/' . $className . '.php';
    	
    	if(!file_exists($classPath))
    		return false;
		
		return $classPath;
    }

  
}