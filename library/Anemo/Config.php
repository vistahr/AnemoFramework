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
 * Config class handle ini files
 * @author vince
 * @version 1.0
 */
class Config 
{
	
	public $file 			= "";
	public $fileInfoArray 	= array();
	public $filePath 		= "";
	
	/**
	 * Constructor initialize the Config file
	 * @param string $file
	 * @param string $path
	 * @return void
	 */
	public function __construct($file,$path) {
		$this->file 			= $file;
		$this->fileInfoArray	= pathinfo($file);
		$this->filePath			= $path;
	}
	
	/**
	 * Returns the config file as an array
	 * @return array $configArray
	 */
	public function toArray() {
		$configArray = array();
		
		if($this->checkConfigFile())
			$configArray = parse_ini_file($this->filePath . '/' . $this->file,true);
		
		return $configArray;
	}
	
	/**
	 * Returns config filename
	 * @return string
	 */
	public function getFilename() {
		return $this->fileInfoArray['filename'];
	}
	
	/**
	 * Verify the configfilename
	 * @throws Config\Exception
	 * @return boolean
	 */
	protected function checkConfigFile() {
		if(strtolower($this->fileInfoArray['extension']) != 'ini') 
			throw new Config\Exception($this->file . 'is no valid configuration file. INI required.');

		if(!is_file($this->filePath . '/' . $this->file))
			throw new Config\Exception('Configuration file ' . $this->file . ' not found.');
			
		return true;
	}
	
	
}