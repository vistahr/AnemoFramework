<?php
namespace Anemo;

class Exception extends \Exception
{
	
	public function __construct($message = null, $code = 0){
        if (!$message)
            throw new $this('Unknown '. get_class($this));
        
        $message = get_class($this) . ': ' . $message;
            
        parent::__construct($message, $code);
    }
    
    public function getMessageByEnvironment() {
    	if(APPLICATION_ENV == 'production') {
    		preg_match("#.+: (.+)#", $this->message, $msg);
    		return $msg[1];
    	}
    	return $this->message;
    }
    
    
}