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
	return $this->_helper->redirector('index','work');
    }
    
    
    
	public function getRule($request){	
		$action = $request->action;
		$resource_id = $request->getParam('id');
		
		switch($action){
			case 'feedback': 
					if($request->isPost()){
						$rule =array('privilege'=> 'feedback','text_id' => null) ;
					}else{$rule =array('privilege'=> 'feedback','text_id' => null, 'notAllowed'=>true) ;}
					break;
		default: $rule = 'noAction';		
		}
		return $rule;
	}
}
