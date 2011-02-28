<?php
namespace Anemo\Import\Adapter;

class CSV extends AdapterAbstract implements AdapterInterface
{
	
	protected $delimiter = ',';
	protected $enclosure = '"';
	protected $escape    = '\\';
	
	
	public function __construct($params) {
		if(isset($params['delimiter']))
			$this->delimiter = $params['delimiter'];
			
		if(isset($params['enclosure']))
			$this->enclosure = $params['enclosure'];
			
		if(isset($params['escape']))
			$this->escape= $params['escape'];		
	}
	
	
	public function toArray($input) {
		$this->data = $input;
		return str_getcsv($input);
	}
	
	
}