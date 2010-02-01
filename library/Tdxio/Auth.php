<?php

class Tdxio_Auth
{	
	public static function connect(){
		$auth = Zend_Auth::getInstance();	
		return $auth;
	}
	
	public static function getUserId(){
		$auth = self::connect();
		if ($auth->hasIdentity()) {
            $userid = $auth->getIdentity();
			return $userid;
		}
		return null;		
	}
	
	public static function getUserName(){
		if (null !== ($userid = self::getUserId())) {
            if (preg_match('/cn\=([a-zA-Z0-9.]*)/',$userid,$matches)) {
			   return $matches[1];			   
           }
		}
		return null;
	}
	
	public static function getUserRole(){
		return 'member'; //temporaneo
		//dovrò decidere in seguito se chiamare da qui il modello
		// o se delegare questa funzione ad un altra classe
	}
	

}