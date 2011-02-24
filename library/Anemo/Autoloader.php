<?php
namespace Anemo;

require_once 'Anemo/Autoloader/Exception.php';

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