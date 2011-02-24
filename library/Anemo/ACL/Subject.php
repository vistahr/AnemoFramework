<?php
namespace Anemo\ACL;

class Subject
{
	
	protected $subject = "";
	
	
	public function __construct($subject) {
		$this->subject = $subject;
	}
	
	
	public function getSubject() {
		return $this->subject;
	}
	
	public function __toString() {
		return $this->getSubject();
	}
	
	
}