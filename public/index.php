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
	|| define('TEMP_PATH', realpath(dirname(__FILE__) . '/../public/temp'));
	
function __($text)
{
    if (!false) {
        // if not found a translation, send back the original text
        return $text;
    }
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
