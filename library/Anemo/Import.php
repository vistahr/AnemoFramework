<?php
namespace Anemo;



class Import
{

	public static function factory($adapter, $params = array()) {
		
		if(!is_string($adapter) || !trim($adapter) )
            throw new Import\Exception('No valid Adapter');
            
		if(!$adapter = new $adapter($params))
			throw new Import\Exception('Cannot instantiate the Adapter');
		
		if(!$adapter instanceof Import\Adapter\AdapterInterface)
			throw new Import\Exception('Adapter must implementthe interface');
		
		return $adapter;
	}
	
}