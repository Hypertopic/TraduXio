<?php

/**
 * Taggable controller
 *
 * 
 * @uses       Tdxio_Controller_Abstract
 * @package    Traduxio
 * @subpackage Controller
 */ 
 
class TaggableController extends Tdxio_Controller_Abstract
{
    protected $_modelname='Taggable'; 
    
    protected function tagSentence()
    {
        
    }

    public function tag($taggable_id,$data){
        
        $model= $this->_getModel();
        $user = Tdxio_Auth::getUserName();
        $tag = array('username'=> $user, 'taggable_id'=> $taggable_id, 'comment' => $data['tag_comment']);
        $model->tag($tag);
    }
     
    public function deletetagAction(){        
        $request=$this->getRequest();
        $translationId=$request->getParam('taggable_id');
        $tagId=$request->getParam('tag_id');
        $model= $this->_getModel();
        $model->deleteTag($tagId,$translationId);
        $this->_redirect($_SERVER['HTTP_REFERER']);
        $this->_helper->redirector->gotoSimple('edit',null,null,array('id'=>$translationId));
    
    }
    
    public function getRule($request){
        $action = $request->action;
        $resource_id = $request->getParam('id');
        $rule = 'noAction';
    
        switch($action){
            case 'deletetag': 
                        $rule = 'noAction' /* DA MODIFICARE */;        
                        break; 
        }               
        return $rule;
        
    }
    
    
}
