<?php

class Tdxio_Plugin_PrefPlugin extends Zend_Controller_Plugin_Abstract
{
    public $_userid;
    public $_role;
    //public $_langFiles = array('en'=>'en','fr'=>'fr_FR','it'=>'it_IT');
    
    public function __construct()
    {
        $this->_userid = Tdxio_Auth::getUserName();
        $this->_role = Tdxio_Auth::getUserRole();
    }

    public function preDispatch($request)
    {
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        Tdxio_Log::info($controller,'controller');
        Tdxio_Log::info($action,'action');        
        Tdxio_Log::info($_SESSION,'session');
        Tdxio_Log::info('flusso: 5 PLUGIN PREDISPATCH');
        
        $options = $this->getPref();
        $this->setCurPref($options);
        
        if(!empty($controller)){
            $langform = new Form_LangSel();
            Tdxio_Log::info($request->isPost(),'ispost');
            if($request->isPost()){
                if ($langform->isValid($this->getRequest()->getPost())) {
                
                    Tdxio_Log::info('flusso: 6 PLUGIN POST FORM');
                    $lang_option = $langform->getValues();
                    $this->setCurPref($lang_option);
                    $langform = new Form_LangSel();
                    $this->_response->setRedirect($request->getRequestUri());
                }
            }                  
        }
        $layout = Zend_Controller_Action_HelperBroker::getStaticHelper('Layout');
        $view = $layout->getView();
        $view->langform=$langform;     
        $view->prefs = $options;
    }
        

    public function postDispatch($request)
    {
        Tdxio_Log::info('flusso: 7 PLUGIN POSTDISPATCH');
        
        $options = $this->getPref();
        if(!is_null($this->_userid)){
            Tdxio_Preferences::setDbPrefs($this->_userid,$options);
        }
    }
    
    protected function setCurPref(array $options){
        Tdxio_Log::info($options,'flusso: 9 PLUGIN SETCURPREF');
        Tdxio_Preferences::setSessionPrefs($options);
        Tdxio_Preferences::setCurLanguage($options);
    }
    
    public function getPref($opt_name=null){
        Tdxio_Log::info('flusso: 10 PLUGIN GETPREF');
        $options = array();
        $options = Tdxio_Preferences::getSessionPrefs();
        if(empty($options)){
            $options = Tdxio_Preferences::getDbPrefs($this->_userid);
        }
        if(empty($options)){
            try{
                $options = Zend_Registry::get('preferences');
            }catch(Zend_Exception $e){
                Tdxio_Log::info('There are no stored options in zend registry');
            }    
        }
        return $options;
    }
    
    
}
