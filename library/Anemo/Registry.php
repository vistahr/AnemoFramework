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
 * Registry pattern allows to store objects permanent in a session
 * @abstract
 * @author vince
 * @version 1.0
 */
abstract class Registry
{
	
    /**
     * Object registry provides storage for shared objects
     * @var array 
     */
    private static $registry = array();
    private static $null = null;
    
    /**
     * Adds a new variable to the Registry.
     * @param string $key Name of the variable
     * @param mixed $value Value of the variable
     * @throws Registry\Exception
     * @return bool 
     */
    public static function set($key, &$value) {
        if ( ! self::has($key) ) {
            self::$registry[$key] = $value;
            return true;
        } else {
            throw new Registry\Exception('Unable to set variable `' . $key . '`. It was already set.');
        }
    }

    /**
     * Tests if given $key exists in registry
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        if ( isset( self::$registry[$key] ) ) {
            return true;
        }

        return false;
    }
 
    /**
     * Returns the value of the specified $key in the Registry.
     * @param string $key Name of the variable
     * @return mixed Value of the specified $key
     */
    public static function &get($key)
    {
        if ( self::has($key) ) {
            return self::$registry[$key];
        }
        return self::$null;
    }
 
    /**
     * Returns the whole Registry as an array.
     * @return array Whole Registry
     */
    public static function getAll()
    {
        return self::$registry;
    }
 
    /**
     * Removes a variable from the Registry.
     * @param string $key Name of the variable
     * @return bool
     */
    public static function remove($key)
    {
        if ( self::has($key) ) {
            unset(self::$registry[$key]);
            return true;
        }
        return false;
    }
 
    /**
     * Removes all variables from the Registry.
     * @return void 
     */
    public static function removeAll()
    {
        self::$registry = array();
        return;
    }
}