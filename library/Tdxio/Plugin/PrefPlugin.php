<?php

//require_once APPLICATION_PATH.'/models/PrivilegeModel.php'; 

class Tdxio_Plugin_PrefPlugin extends Zend_Controller_Plugin_Abstract
{
    public $_userid;
    public $_role;
    public $_langFiles = array('en'=>'en','fr'=>'fr_FR','it'=>'it_IT','eng'=>'en','fra'=>'fr_FR','ita'=>'it_IT');
    
    public function __construct()
    {
        $this->_userid = Tdxio_Auth::getUserName();
        $this->_role = Tdxio_Auth::getUserRole();
    }

    public function preDispatch($request)
    {
        //Tdxio_Log::info($this->_defaultOptions,'locale pref construct');
        Tdxio_Log::info('flusso: 5 PLUGIN PREDISPATCH');
        $options = $this->getPref();
        $this->setCurPref($options);//Zend_Registry::set('preferences',$options);
        //$this->setCurLanguage($options);
        
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        
        if(!empty($controller)){
        $langform = new Form_LangSel();
            Tdxio_Log::info('DEBUGGG');
            Tdxio_Log::info($controller,'controller');
            Tdxio_Log::info($action,'action');
            Tdxio_Log::info($request->isPost(),'ispost');
            if($request->isPost()){
                if ($langform->isValid($this->getRequest()->getPost())) {
                
                    Tdxio_Log::info('flusso: 6 PLUGIN POST FORM');
                    Tdxio_Log::info('prefplugin predispatch');
                    $lang_option = $langform->getValues();
                    $this->setCurPref($lang_option);
                    Tdxio_Log::info($lang_option,'langoption prefplugin');
                    $langform = new Form_LangSel();
                    $this->_response->setRedirect($request->getRequestUri());
                }
            }
        }
            
        
        $layout = Zend_Controller_Action_HelperBroker::getStaticHelper('Layout');
        $view = $layout->getView();
        $view->langform=$langform;
        $view->prefs = $options;/* */
    }
        

    public function postDispatch($request)
    {
        Tdxio_Log::info('flusso: 7 PLUGIN POSTDISPATCH');
        try{
            $options = Zend_Registry::get('preferences');
            $this->savePref($options);
        }catch(Zend_Exception $e){
            Tdxio_Log::info('Failed to save options in the db, there are no stored options in zend resgistry');
        }
    }
    
    protected function savePref($options){
        Tdxio_Log::info('flusso: 8 PLUGIN SAVEPREF');
        if(!is_null($this->_userid)){
            $userModel = new Model_User();
            $userModel->setOptions($this->_userid,$options);
        }        
    }
    
    protected function setCurPref(array $options){
        Tdxio_Log::info('flusso: 9 PLUGIN SETCURPREF');
        //refreshes stored preferences with passed values, if not null
        try{
            $stored_options = Zend_Registry::get('preferences');
        }catch(Zend_Exception $e){$stored_options = array();}
        $newoptions = $stored_options;
        if(!empty($options)){
            foreach($options as $optKey=>$value){
                if(!empty($value)){
                    $newoptions[$optKey]=$value;
                }
            } 
        }
        Zend_Registry::set('preferences',$newoptions);
        $this->setCurLanguage($newoptions);
    }
    
    protected function getPref($opt_name=null){
        Tdxio_Log::info('flusso: 10 PLUGIN GETPREF');
        
        $options = Zend_Registry::get('preferences');  
               
        //if there are any user preferences in the db, refresh $options with those values
        if(!is_null($this->_userid)){
            $userModel = new Model_User();
            $user_options = $userModel->getOptions($this->_userid,$opt_name);
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
    
    //actuates language changes on the base of passed options
    protected function setCurLanguage($options){
        Tdxio_Log::info('flusso: 11 PLUGIN SETCURLANG');
        if(isset($options['lang'])){
            $lanPref = $this->_langFiles[$options['lang']];
            Tdxio_Log::info($options['lang']);
            Tdxio_Log::info($lanPref,'aaaaa');            
            if(!empty($lanPref)){
                $translate = Zend_Registry::get('Zend_Translate');
                $translate->addTranslation(APPLICATION_PATH.'/../languages/'.$lanPref.'.mo',substr($lanPref, 0,2));        
                Tdxio_Log::info($translate->getLocale(),'locale after getPref');                    
            }
        }
    }

}
