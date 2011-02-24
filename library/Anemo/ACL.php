<?php 
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


