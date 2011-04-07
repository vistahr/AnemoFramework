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

use Anemo\ID;

/**
 * The id class holds different user data, permanent in a session
 * @author vince
 * @version 1.0
 */
class ID
{
	private static $instance = null;
	
	private $IDObject = array('userData'  	   => array(),
							  'userModel' 	   => null,
							  'subject' 	   => null,
							  'defaultSubject' => null);
	

	private static $AF_SESSION_ID = 'ANEMOFRAMEWORK_ID_CLASS';
	
	private function __construct() {}
	private function __clone() {}
	
	
	public static function getInstance(){
		if(self::$instance === null){
	    	self::$instance = new ID();
	    }
	    return self::$instance;
	}	
	
	
	public function setUserData($key, $value) {
		$this->update();
		$this->IDObject['userData'][$key] = $value;
		$this->save();
	}
	
	public function getUserData($key) {
		$this->update();
		return $this->IDObject['userData'][$key];
	}
	
	public function setUserModel($userModel) {
		$this->update();
		$this->IDObject['userModel'] = $userModel;
		$this->save();
	}
	
	public function getUserModel() {
		$this->update();
		return $this->IDObject['userModel'];
	}
	
	
	public function setDefaultSubject(ACL\Subject $defaultSubject) {
		$this->update();
		$this->IDObject['defaultSubject'] = $defaultSubject;
		$this->save();
	}
	
	public function setSubject(ACL\Subject $subject) {
		$this->update();
		$this->IDObject['subject'] = $subject;
		$this->save();
	}
	
	public function getSubject() {
		$this->update();
		if($this->IDObject['subject'] instanceof ACL\Subject)
			return $this->IDObject['subject']->getSubject();
		
		return $this->IDObject['defaultSubject']->getSubject();
	}
	
	
	public function __toString() {
		$this->update();
		if($this->IDObject['subject'] instanceof ACL\Subject)
			return $this->IDObject['subject']->getSubject();
		return "";
	}
	
	
	public function isLogged() {
		$this->update();
		
		if($this->IDObject['defaultSubject'] === null)
			throw new ID\Exception('Defaultsubject not set');
		
		if(isset($this->IDObject['subject']) && $this->IDObject['subject'] !== null && $this->IDObject['defaultSubject']->getSubject() != $this->IDObject['subject']->getSubject())
			return true;
		
		return false;
	}
	
	
	public function logout() {
		$this->IDObject['subject']  = null;
		$this->IDObject['userData'] = array();
		$this->save();
	}

	
	public function serialize() {
		\Anemo\Session::setSession(ID::$AF_SESSION_ID, serialize($this));
	}
	
	public function unserialize() {
		return unserialize(\Anemo\Session::getSession(ID::$AF_SESSION_ID));
	}
	
	public function save() {
		$this->serialize();
	}
	
	public function update() {
		$obj = $this->unserialize();
		$this->IDObject = $obj->IDObject;
	}
	
	
}