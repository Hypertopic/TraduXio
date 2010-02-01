<?php

/**
 * Index controller
 *
 * Default controller for this application.
 * 
 * @uses       Zend_Controller_Action
 * @package    Traduxio
 * @subpackage Controller
 */
class IndexController extends Zend_Controller_Action
{
    /**
     * The "index" action is the default action for all controllers -- the 
     * landing page of the site.
     *
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     * - /
     * - /index/
     * - /index/index
     *
     * @return void
     */
    public function indexAction()
    {
        /*
           There is nothing inside this action, but it will still attempt to 
           render a view.  This is because by default, the front controller 
           uses the ViewRenderer action helper to handle auto rendering
           (In the MVC grand scheme of things, the ViewRenderer allows us to 
           draw the line between the C and V in the MVC.  Also note this is by 
           default on, but optional).
        */
    }
}
