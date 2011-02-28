<?php
namespace Anemo\Import\Adapter;

class XML extends AdapterAbstract implements AdapterInterface
{
	
	public function __construct($params) {
	}
	
	
	public function toArray($input) {
		$this->data = $input;
		$xml = simplexml_load_string($input);
		return $this->object2array($xml);
	}	
	
}