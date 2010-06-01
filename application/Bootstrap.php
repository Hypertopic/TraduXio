<?php

/**
 * Application bootstrap
 * 
 * @uses    Zend_Application_Bootstrap_Bootstrap
 * @package Traduxio
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Bootstrap autoloader for application resources
     * 
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath'  => dirname(__FILE__),
        ));
        
         // $autoloader->addResourceTypes(array(
             // 'model' => array(
                // 'path'      => 'models',
                // 'namespace' => 'Model',
             // ),
            // 'form' => array(
                // 'path'      => 'forms',
                // 'namespace' => 'Form',
            // ),
             // 'controller' =>array(
                 // 'path'      => 'controllers',
                 // 'namespace' => 'Controller',
             // ),
          // ));
        
        // return $autoloader;
    }

    public function run() // preso da DODO e parzialmente modificato
    {
        // make the config available to everyone
        Zend_Registry::set('config', new Zend_Config($this->getOptions()));
        
        parent::run();
    }
    /**
     * Bootstrap the view doctype
     * 
     * @return void
     */
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }
    
    //INITIALIZE FRONT CONTROLLER
    protected function _initFront(){
        $front = Zend_Controller_Front::getInstance();
    }
    
    //SET LAYOUT PATH
    protected function _initLayout(){
        Zend_Layout::startMvc(APPLICATION_PATH . '/layouts/scripts');
    }
    
    //SET PLUGIN PATHS
    protected function _initPlugins(){
        $loader = new Zend_Loader_PluginLoader(array(
        'Tdxio_Plugin' => APPLICATION_PATH . '/../library/Tdxio/Plugin/'
        ));

        $front = Zend_Controller_Front::getInstance();
        $aclPlugin = new Tdxio_Plugin_AclPlugin();
        $front->registerPlugin($aclPlugin); 
        $prefPlugin = new Tdxio_Plugin_PrefPlugin();
        $front->registerPlugin($prefPlugin); 
     //   $translatePlugin = new Tdxio_Plugin_TranslatePlugin();
     //   $front->registerPlugin($translatePlugin); 

    }
    
    protected function _initProfiler() {
            $this->bootstrap('db');
            $res=$this->getPluginResource('db');
            $dbAdapter=$res->getAdapter();
            
            $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
            $profiler->setEnabled(true);
            
            //$dbAdapter->setProfiler($profiler);

    }
        
    //INITIALIZE HELPER PATH
    protected function _initHelperPath(){
    
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH.'controllers/helpers');
        
        $view = new Zend_View();
        $view->setHelperPath(APPLICATION_PATH.'views/helpers');
    }
    
    //INITIALIZE FORM PATH
    protected function _initFormPath(){
    }
    
    //INITIALIZE LOGGER
    protected function _initLogger(){
        $writer = new Zend_Log_Writer_Stream(TEMP_PATH.'/log.txt');
        $logger = new Zend_Log($writer);
    }
    
    //INITIALIZE TRANSLATE    
    protected function _initTranslate(){
       $translate = Zend_Registry::get('Zend_Translate');
       
       $files = scandir(APPLICATION_PATH.'/../languages');
       $mo_files = array_filter($files,"Tdxio_Preferences::mo");
       
       $languages = array();
       $cur_lang = $translate->getLocale();
       
       foreach($mo_files as $key=>$name){
           $name = substr($name,0,strlen($name)-3);
           $translate->addTranslation(APPLICATION_PATH.'/../languages/'.$name.'.mo',$name);
           $languages[$name]=$translate->_($name,$name);
       }
       $translate->setLocale($cur_lang);
       
       Zend_Registry::set('languages',$languages);   
    }
    
}
