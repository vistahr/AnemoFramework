<?php
namespace Anemo;

use Anemo\ID;


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