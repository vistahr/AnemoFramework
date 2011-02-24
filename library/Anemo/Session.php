<?php
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
	
}