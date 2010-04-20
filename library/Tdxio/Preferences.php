<?php

class Tdxio_Preferences
{
    public static function getSessionPrefs(){
        $tdxioPrefs = new Zend_Session_Namespace('Tdxio_Prefs');
        $prefs = array();
        if(isset($tdxioPrefs->preferences)){
            $prefs = $tdxioPrefs->preferences;
            Tdxio_Log::info($prefs,'GETPREFLANG: session options exist');
        }
        return $prefs;        
    }
    
    public static function setSessionPrefs($options){
        $ses_options=self::getSessionPrefs();
        if(empty($ses_options)){
            $ses_options = $options;
        }else{
            foreach($options as $name=>$opt){
                if(!empty($opt)){
                    $ses_options[$name]=$opt;
                    Tdxio_Log::info($opt,'opt');
                }
            }
        }
        $tdxioPrefs = new Zend_Session_Namespace('Tdxio_Prefs');
        $tdxioPrefs->preferences=$ses_options;
        Tdxio_Log::info($_SESSION,'session after setsessionprefs');
        return $ses_options;   
    }
    
    public static function getDbPrefs($user){
        $options = Zend_Registry::get('preferences');  
               
        //if there are any user preferences in the db, refresh $options with those values
        if(!is_null($user)){
            $userModel = new Model_User();
            $user_options = $userModel->getOptions($user,$opt_name);
            if(!empty($user_options)){// if the user has no preferences, return the default ones
                foreach($user_options as $key=>$val){
                    if(!empty($val)){
                        $options[$key]=$val;
                    }
                }                
            }
        }
        return $options;
    }
    
    public static function setDbPrefs($user,$options){

        Tdxio_Log::info('flusso: 8 PREFERENCES SETDBPREFS');
        if(!is_null($user)){
            $userModel = new Model_User();
            $userModel->setOptions($user,$options);
        }   
    }
    
}
