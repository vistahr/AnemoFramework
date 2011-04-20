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

use Anemo\Validate\Exception;

use Validate\Adapter;

class Validate
{
	
	
	/**
	 * Validate an input with one Validator
	 * @param string $input
	 * @param array $validator
	 * @return boolean
	 */
	public static function check($input, array $validator) {
		if(!is_array($validators))
			throw new Validate\Exception('Validator is not an array');
			
		Validate::validate($input, $validatorName, $validatorParams);
	}
	
	/**
	 * Validate an input with one or more Validators
	 * @param string $input
	 * @param array $validators
	 * @return boolean
	 */
	public static function chain($input, array $validators) {
		
		
		foreach($validators as $v) {
			
		}
	}
	
	
	private static function validate($input, $validatorName, $validatorParams) {
		$adapter = 'Anemo\Validate\Adapter\\' . $validatorName;
		$validatorObject = Validate::factory($adapter);
	}
	
	/**
	 * The factory method implements the factory pattern and loads dynamically the given validate adapter
	 * @param string $adapter
	 * @param array $params
	 * @throws Validate\Exception
	 * @return Validate\Adapter\AdapterAbstract
	 */
	private static function factory($adapter, array $params) {
		
		if(!is_string($adapter) || !trim($adapter) )
            throw new Validate\Exception('No valid adapter');
         
		if(!$adapter = new $adapter($params))
			throw new Validate\Exception('Cannot instantiate the adapter');
		
		if(!$adapter instanceof Validate\Adapter\AdapterAbstract)
			throw new Validate\Exception('Adapter must implementthe interface');
		
		return $adapter;
	}
	
	
}