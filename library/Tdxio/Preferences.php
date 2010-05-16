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
    
        //if there are any user preferences in the db, refresh $options with those values
        if(!is_null($user)){
            $userModel = new Model_User();
            $user_options = $userModel->getOptions($user,$opt_name);
            if(!empty($user_options)){
                foreach($user_options as $key=>$val){
                    if(!empty($val)){
                        $options[$key]=$val;
                    }
                }                
            }
        }
        if(empty($options)){// if the user has no preferences, return the default ones
            try{
                $options = Zend_Registry::get('preferences');
                Tdxio_Log::info($options,'registry options');
            }catch(Zend_Exception $e){
                Tdxio_Log::info('There are no stored options in zend registry');
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
    
    public static function mo($filename){
        if((preg_match('/.mo$/',$filename)>0)AND(preg_match('/^languages/',$filename)<1))
            return true;
        else return false;
            
    }
    
    public static function getCurLanguage(){
        Tdxio_Log::info('flusso: 14 PREFERENCES GETCURLANGUAGE');
        $translate = Zend_Registry::get('Zend_Translate');
        Tdxio_Log::info($translate->getLocale(),'locale getcurlanguage');
        return $translate->getLocale();
    }
    
    public static function setCurLanguage($options){
        Tdxio_Log::info('flusso: 11 PLUGIN SETCURLANG');
        if(isset($options['lang'])){
            Tdxio_Log::info($options['lang'],'isset');
            $lanPref = $options['lang'];
            if(!empty($lanPref)){
                $translate = Zend_Registry::get('Zend_Translate');
                try{   
                    $translate->setLocale($lanPref);
                }catch(Zend_Exception $e){
                    Tdxio_Log::info($e,'ERRORE IN SETCURLANGUAGE');
                }
                Tdxio_Log::info($translate->getLocale(),'locale after setCurLanguage');                    
            }
        }
    }
    
    public static function getPref($opt_name=null){
        Tdxio_Log::info('flusso: 10 PLUGIN GETPREF');
        $options = array();
        $options = self::getSessionPrefs();
        Tdxio_Log::info($options,'session options');
        if(empty($options)){
            $user = Tdxio_Auth::getUserId();
            $options = self::getDbPrefs($user);
            Tdxio_Log::info($options,'db options');
        }       
        Tdxio_Log::info($options,'opzioni all\'inizio');
        if(!is_null($opt_name)){$options = array($opt_name=>$options[$opt_name]);}
        return $options;
    }
    
    public static function setCurPref(array $options){
        Tdxio_Log::info($options,'flusso: 9 PLUGIN SETCURPREF');
        self::setSessionPrefs($options);
        self::setCurLanguage($options);
    }
}
