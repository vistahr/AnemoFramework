<?php
namespace Anemo;

class Config 
{
	
	public $file 			= "";
	public $fileInfoArray 	= array();
	public $filePath 		= "";
	
	
	public function __construct($file,$path) {
		$this->file 			= $file;
		$this->fileInfoArray	= pathinfo($file);
		$this->filePath			= $path;
	}
	
	
	public function toArray() {
		
		$configArray = array();
		
		if($this->checkConfigFile())
			$configArray = parse_ini_file($this->filePath . '/' . $this->file,true);
		
		return $configArray;
	}
	
	
	public function getFilename() {
		return $this->fileInfoArray['filename'];
	}
	
	
	protected function checkConfigFile() {
		if(strtolower($this->fileInfoArray['extension']) != 'ini') 
			throw new Config\Exception($this->file . 'is no valid configuration file. INI required.');

		if(!is_file($this->filePath . '/' . $this->file))
			throw new Config\Exception('Configuration file ' . $this->file . ' not found.');
			
		return true;
	}
	
	
}