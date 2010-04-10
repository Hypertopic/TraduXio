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

function __($text)
{
    $args=func_get_args();
	static $translate=null;
	if (!$translate) {
        $translate = new Zend_Translate('gettext',APPLICATION_PATH.'/../languages/en.mo','en');        
        $actual = $translate->getLocale();
        Tdxio_Log::info($actual,'default_locale');
        $translate->addTranslation(APPLICATION_PATH.'/../languages/it_IT.mo','it');
        $translate->addTranslation(APPLICATION_PATH.'/../languages/fr_FR.mo','fr');
        $translate->setLocale("fr_FR");
    }
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
