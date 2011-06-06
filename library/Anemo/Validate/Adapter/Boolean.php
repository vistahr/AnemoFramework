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

namespace Anemo\Validate\Adapter;

/**
 * Boolean validates all bool inputs
 * @author vince
 * @version 1.0
 */
class Boolean extends ValidateAbstract implements ValidateInterface
{
	
	/**
	 * (non-PHPdoc)
	 * @see Anemo\Validate\Adapter.ValidateInterface::validateInput()
	 */
	public function validateInput($input) {
		
	
		if($this->validatorParams[0] == true) {
			if(is_bool($input)) {
				return true;
			} else if(!is_bool($input) && ((int) $input == 0 || (int) $input == 1)) {
				return true;
			} else {
				return false;
			}
			
		} else if($this->validatorParams[0] == false) {
			if(is_bool($input)) {
				return false;
			} else if(!is_bool($input) && ((int) $input == 0 || (int) $input == 1)) {
				return false;
			} else {
				return true;
			}
				
		} else {
			throw new Alpha\Exception('Non valid parameter in use. Only true or false allowed.');
		}
	}

}
