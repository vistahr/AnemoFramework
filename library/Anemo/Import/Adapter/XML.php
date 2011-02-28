<?php
namespace Anemo\Import\Adapter;

class XML extends AdapterAbstract implements AdapterInterface
{
	
	public function __construct($params) {
		libxml_use_internal_errors(true);
		
	}
	
	
	public function toArray($input) {
		$this->data = $input;
	
		$input = stripslashes($input);
		$xml = simplexml_load_string($input);
		
		$errors = libxml_get_errors();
		if(count($errors) != 0) {
			$errMsg = "";
			foreach($errors as $e) {
				$errMsg .= $e->message . ' ';
			}
			throw new XML\Exception($errMsg);
		}
		
		return $this->object2array($xml);
	}	
	
}