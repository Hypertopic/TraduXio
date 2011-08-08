<?php

/**
 * Error controller
 *
 * @uses       Tdxio_Controller_Abstract
 * @package    Traduxio
 * @subpackage Controller
 */
class ErrorController extends Tdxio_Controller_Abstract
{
    /**
     * errorAction() is the action that will be called by the "ErrorHandler" 
     * plugin.  When an error/exception has been encountered
     * in a ZF MVC application (assuming the ErrorHandler has not been disabled
     * in your bootstrap) - the Errorhandler will set the next dispatchable 
     * action to come here.  This is the "default" module, "error" controller, 
     * specifically, the "error" action.  These options are configurable, see 
     * {@link http://framework.zend.com/manual/en/zend.controller.plugins.html#zend.controller.plugins.standar
     *
     * @return void
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = __('Page not found');
                break;

            default:
                // application error 
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = __('Application error');
                break;
        }
        $this->view->env       = $this->getInvokeArg('env'); 
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }
	
	public function deniedAction()
    {		
    }
        
	
	public function ajaxdeniedAction()
    {
        $request = $this->getRequest();
        $isXml = $request->isXmlHttpRequest();
        Tdxio_Log::info($isXml,'isXml');        
        if($this->view->isMember)
			$this->view->message = array('code'=>3,'text'=>__("You don't have the right to perform this action."));
		else
			$this->view->message = array('code'=>2,'text'=>$this->view->makeUrl('/login/'));
    }
    
    
	

}
