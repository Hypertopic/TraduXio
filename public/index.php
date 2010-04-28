<?php
// Set the initial include_path. You may need to change this to ensure that 
// Zend Framework is in the include_path; additionally, for performance 
// reasons, it's best to move this to your web server configuration or php.ini 
// for production.

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__) . '/../library'),
    get_include_path(),
)));
 
 
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

//Define path to temporary files
defined('TEMP_PATH')
    || define('TEMP_PATH', realpath(dirname(__FILE__) . '/../temp'));
    
require_once 'Zend/Translate.php';
require_once 'Tdxio/Log.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Exception.php';

$translate = new Zend_Translate('gettext',APPLICATION_PATH.'/../languages/en.mo','en'); 
Zend_Registry::set('Zend_Translate',$translate);
$langname = 'en';

try{//refresh preferences with browser informations
    $locale = new Zend_Locale(Zend_Locale::BROWSER);
    $lan = $locale->getLanguage();
    //$reg = $locale->getRegion();
    //$langname = ($reg=='')?$lan:$lan.'_'.$reg;
    $langname = $lan;
}catch(Zend_Locale_Exception $e) {//throw new Zend_Exception('la lingua del browser non è presa');
}

Zend_Registry::set('preferences',array('lang'=>$langname,'color'=>1));       



/*
try{    
    $locale = new Zend_Locale(Zend_Locale::BROWSER);
    $lan = $locale->getLanguage();
    $reg = $locale->getRegion();
    $translate->addTranslation(APPLICATION_PATH.'/../languages/'.$lan.'_'.$reg.'.mo',$lan);
    Zend_Registry::set('default_options',array('lang'=>$lan,'color'=>1));       
}catch(Zend_Locale_Exception $e) {
    $locale = $translate->getLocale();
}
Zend_Registry::set('Zend_Locale',$locale);*/



function __($text)
{
    $args=func_get_args();
    /*
	static $translate=null;
	if (!$translate) {
        $translate = new Zend_Translate('gettext',APPLICATION_PATH.'/../languages/en.mo','en');        
        try{
        $locale = new Zend_Locale(Zend_Locale::BROWSER);
        $lan = $locale->getLanguage();
        $reg = $locale->getRegion();
        $translate->addTranslation(APPLICATION_PATH.'/../languages/'.$lan.'_'.$reg.'.mo',$lan);
    }catch(Zend_Locale_Exception $e) {
        $locale = $translate->getLocale();
    }
        Tdxio_Log::info($locale,'locale browser');
        
    }*/
    $translate = Zend_Registry::get('Zend_Translate');
    $trText = $translate->_($text);
    if (!is_null($trText)) {
        array_shift($args);
        array_unshift($args,$trText);
    }
    return call_user_func_array("sprintf",$args);
}
    
/** Zend_Application */
require_once 'Zend/Application.php';  

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();
$application->run();
