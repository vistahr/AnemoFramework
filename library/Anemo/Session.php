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


class Session
{
	
	
	public static function start() {
		if(!session_start())
			throw new Session\Exception('Session could not start');
	}
	
	
	public static function getSessionID() {
		if(!session_start())
			throw new Session\Exception('No session available');
		return session_id();
	}
	
	
	public static function end() {
		$_SESSION = array();
		if (ini_get('session.use_cookies')) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
	}
	
	 
	public static function setSession($key, $value) {
		$_SESSION[$key] = $value;
	}
	
	public static function getSession($key) {
		return $_SESSION[$key];
	}
	
}