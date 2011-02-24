<?php
namespace Anemo\ACL;

class Resource 
{
	
	protected $resource = "";
	
	
	public function __construct($resource) {
		$this->resource = $resource;
	}
	
	public function getResource() {
		return $this->resource;
	}	
	
	public function __toString() {
		return $this->getResource();
	}	
	
}