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
 * The id class holds different user dataof one user in a session. Auth functions are also available.
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
	
	
	/**
	 * Get the instance (Singleton)
	 * @return Anemo\ID $this
	 */
	public static function getInstance(){
		if(self::$instance === null){
	    	self::$instance = new ID();
	    }
	    return self::$instance;
	}	
	
	/**
	 * Set data with key, value pairs
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function setUserData($key, $value) {
		$this->update();
		$this->IDObject['userData'][$key] = $value;
		$this->save();
	}
	
	/**
	 * Getter - user data
	 * @param string $key
	 * @return string
	 */
	public function getUserData($key) {
		$this->update();
		return $this->IDObject['userData'][$key];
	}
	
	/**
	 * Set a model
	 * @param object $userModel
	 * @return void
	 */
	public function setUserModel($userModel) {
		$this->update();
		$this->IDObject['userModel'] = $userModel;
		$this->save();
	}
	
	/**
	 * Getter - user model
	 * @return object
	 */
	public function getUserModel() {
		$this->update();
		return $this->IDObject['userModel'];
	}
	
	/**
	 * Set the default acl subject of the current user. This is the required fallback subject.
	 * @param ACL\Subject $defaultSubject
	 * @return void
	 */
	public function setDefaultSubject(ACL\Subject $defaultSubject) {
		$this->update();
		$this->IDObject['defaultSubject'] = $defaultSubject;
		$this->save();
	}
	
	/**
	 * Set the acl subject of the current user
	 * @param ACL\Subject $subject
	 * @return void
	 */
	public function setSubject(ACL\Subject $subject) {
		$this->update();
		$this->IDObject['subject'] = $subject;
		$this->save();
	}
	
	/**
	 * Return the subject of the current user. If no subject is setted, the default subject return.
	 * @return ACL\Subject
	 */
	public function getSubject() {
		$this->update();
		
		if($this->IDObject['subject'] instanceof ACL\Subject)
			return $this->IDObject['subject']->getSubject();
			
		if($this->IDObject['defaultSubject'] === null)
			throw new ID\Exception('Defaultsubject not set');
			
		return $this->IDObject['defaultSubject']->getSubject();
	}
	
	/**
	 * Return the subject, if no exists, an empty string return.
	 * @return string
	 */
	public function __toString() {
		$this->update();
		if($this->IDObject['subject'] instanceof ACL\Subject)
			return $this->IDObject['subject']->getSubject();
		return "";
	}
	
	/**
	 * Check if the current subject is not equal the default subject.
	 * @throws ID\Exception
	 * @return boolean
	 */
	public function isLogged() {
		$this->update();
		
		if($this->IDObject['defaultSubject'] === null)
			throw new ID\Exception('Defaultsubject not set');
		
		if(isset($this->IDObject['subject']) && $this->IDObject['subject'] !== null && $this->IDObject['defaultSubject']->getSubject() != $this->IDObject['subject']->getSubject())
			return true;
		
		return false;
	}
	
	/**
	 * Delete the ID object
	 * @return void
	 */
	public function logout() {
		$this->IDObject['subject']  = null;
		$this->IDObject['userData'] = array();
		$this->save();
	}

	/**
	 * Serialize the object and save it in a session
	 * @return void
	 */
	protected function serialize() {
		\Anemo\Session::setSession(ID::$AF_SESSION_ID, serialize($this));
	}
	
	/**
	 * Unserialize the object from the session
	 * @return void
	 */
	protected function unserialize() {
		return unserialize(\Anemo\Session::getSession(ID::$AF_SESSION_ID));
	}
	
	/**
	 * A wrapper for serialize
	 */
	public function save() {
		$this->serialize();
	}
	
	/**
	 * A wrapper for unserialize
	 */
	public function update() {
		$obj = $this->unserialize();
		$this->IDObject = $obj->IDObject;
	}
	
	
}