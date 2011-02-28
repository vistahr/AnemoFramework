<?php
namespace Anemo\Import\Adapter;


abstract class AdapterAbstract
{
	
	protected $data;

	
	function object2array($object) { 
		return @json_decode(@json_encode($object),1); 
	} 
	
}   