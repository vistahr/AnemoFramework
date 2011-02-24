<?php
namespace Anemo;


class ID
{
	private static $instance = null;
	
	private $userID;
	private $userName;
	private $userCrendetial;
	
	protected $userModel = null;
	
	protected $subject = null;
	
	
	private function __construct() {}
	private function __clone() {}

	
	public static function getInstance(){
		if(self::$instance === null){
	    	self::$instance = new ID();
	    }
	    return self::$instance;
	}	
	
	
	public function init() {
		//TODO
	}
	
	public function authenticate() {
		//TODO
	}
	
	
	public function setUserID($userID) {
		$this->userID = $userID;
	}
	
	public function getUserID() {
		return $this->userID;
	}
	
	
	public function setUserName($userName) {
		$this->userName = $userName;
	}
	
	public function getUserName() {
		return $this->userName;
	}
	
	
	public function setUserCredential($userCredential) {
		$this->userCrendetial = $userCredential;
	}
	
	
	public function setUserModel($userModel) {
		$this->userModel = $userModel;
	}
	
	public function getUserModel() {
		return $this->userModel;
	}
	
	public function setSubject(ACL\Subject $subject) {
		$this->subject = $subject;
	}
	public function getSubject() {
		return $this->subject;
	}
	public function __toString() {
		return $this->subject;
	}
	
	
	
	
	
}