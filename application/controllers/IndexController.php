<?php

/**
 * Index controller
 *
 * Default controller for this application.
 * 
 * @uses       Tdxio_Controller_Abstract
 * @package    Traduxio
 * @subpackage Controller
 */
class IndexController extends Tdxio_Controller_Abstract
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
    
    public function tutorialAction()
    {}
   
      
      
    public function aboutAction() {
    }

    public function feedbackAction() {
      
        $form = new Form_Feedback($type);
        $request=$this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values=$form->getValues();
                require_once "send_mail.php";
                try {
                    sendFeedback($values['title'],$values['body'],$values['emailaddress']);
                    $this->view->sent=true;
                } catch (Exception $e) {
                    $this->view->error=$e->getMessage();
                }
            }
        }
        $this->view->form=$form;
    }
    
     
    public function getRule($request){  
        $action = $request->action;
        $resource_id = $request->getParam('id');
        
        switch($action){
            case 'feedback': 
                    if($request->isPost()){
                        $rule =array('privilege'=> 'feedback','work_id' => null) ;
                    }else{$rule =array('privilege'=> 'feedback','work_id' => null, 'notAllowed'=>true) ;}
                    break;
        default: $rule = 'noAction';        
        }
        return $rule;
    }
}
