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

use Anemo\ACL;

class ACL
{
	
	protected $parentSubjects  = array();
	protected $parentResources = array();
	
	protected $acl = array();
	
	protected static $GRANT_ALL_ACCESS = 'all';
	
	
	public function __construct() {}
	
	
	
	public function addSubject(\Anemo\ACL\Subject $subject, $parent = array()) {
		if(!is_array($parent))
			throw new ACL\Exception('Parent, expect array');
			
		$this->parentSubjects[$subject->getSubject()] = $parent;
		return $subject;
	}
	
	public function addResource(\Anemo\ACL\Resource $resource, $parent = array()) {
		if(!is_array($parent))
			throw new ACL\Exception('Parent, expect array');
			
		$this->parentResources[$resource->getResource()] = $parent;
		return $resource;
	}
	
	
	public function allow($subject, $resource = null, $action = array()) {
		
		if($subject instanceof \Anemo\ACL\Subject)
			$subject = $subject->getSubject();
			
		if($resource instanceof \Anemo\ACL\Resource)
			$resource = $resource->getResource();
		
		if(!is_array($action))
			throw new ACL\Exception('Action, expect array');
		
		if(!isset($this->acl[$subject]))
			$this->acl[$subject] = null;
		
		// Alle Rechte setzen
		if(($resource == null && count($action) == 0) || $this->acl[$subject] == ACL::$GRANT_ALL_ACCESS)
			return $this->acl[$subject] = ACL::$GRANT_ALL_ACCESS;
			
		
		$part = &$this->acl[$subject][$resource];
		
		if(!isset($part))
			return $part = $action;
		
		return $part = array_unique(array_merge($part, $action));
	}
	
	
	public function deny($subject, $resource = null, $action = array()) {
		if($subject instanceof \Anemo\ACL\Subject)
			$subject = $subject->getSubject();
			
		if($resource instanceof \Anemo\ACL\Resource)
			$resource = $resource->getResource();
		
		if(!is_array($action))
			throw new ACL\Exception('Action has to be an array');
			
		// Alle Rechte entfernen
		if($resource == null && count($action) == 0) {
			unset($this->acl[$subject]);
			return true;
		}
		
		// Alle Rechte der Resource entfernen
		if(isset($this->acl[$subject][$resource]) && count($action) == 0) {
			unset($this->acl[$subject][$resource]);
			return true;
		}
		
		// Einzelne Actionrechte entfernen
		foreach($this->acl[$subject][$resource] as $aclKey => $acl) {
			foreach($action as $a) {
				if($acl == $a)
					unset($this->acl[$subject][$resource][$aclKey]);
			}
		}
		
		return true;
	}
	
	
	public function isAllowed($subject, $resource, $action) {
		 
		if($subject instanceof \Anemo\ACL\Subject)
			$subject = $subject->getSubject();
			
		if($resource instanceof \Anemo\ACL\Resource)
			$resource = $resource->getResource();
		
		if(!isset($this->parentSubjects[$subject]))
			throw new ACL\Exception('Subject not found');
			
		if(!isset($this->parentResources[$resource]))
			throw new ACL\Exception('Resource not found');

		
		if(isset($this->acl[$subject]) && $this->acl[$subject] == ACL::$GRANT_ALL_ACCESS)
			return true;
		
			
		if(isset($this->acl[$subject][$resource]) && in_array($action,$this->acl[$subject][$resource]))
			return true;	
		
		// Vererbung rekursiv chekcen	
		foreach($this->parentSubjects[$subject] as $parentSub) {
			
			if(count($this->parentResources[$resource])>0) {
				foreach($this->parentResources[$resource] as $parentRes) {
					
					if($this->isAllowed($parentSub, $parentRes, $action) === true)
						return true;
				}
			} else {
				
				if($this->isAllowed($parentSub, $resource, $action) === true)
					return true;
			}
			
		}
		
		return false;
	}
	
}


