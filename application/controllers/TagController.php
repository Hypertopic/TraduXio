<?php

/**
 * Tag controller
 *
 * 
 * @uses       Tdxio_Controller_Abstract
 * @package    Traduxio
 * @subpackage Controller
 */ 
 
class TagController extends Tdxio_Controller_Abstract
{
    protected $_modelname='Taggable'; 
    
    protected function tagSentence()
    {
        
    }

    public function tagAction(){
        
        $model= $this->_getModel();
        $user = Tdxio_Auth::getUserName();
        $request = $this->getRequest();
        $taggable_id = $request->getParam('id');
        $tag = $request->getParam('tag');
        $data = array('username'=> $user, 'taggable_id'=> $taggable_id, 'comment' => $tag);
        $model->tag($data);        
        $this->_redirect($_SERVER['HTTP_REFERER']);
    }
     
    public function deletetagAction(){      
        $username = Tdxio_Auth::getUserName();  
        $request=$this->getRequest();
        $taggableId=$request->getParam('id');
        $tag=$request->getParam('tag');
        $model= $this->_getModel();
        $result = $model->deleteTag($username,$taggableId,$tag);
        Tdxio_Log::info($result,'esito');
        $this->_redirect($_SERVER['HTTP_REFERER']);
   
    }
    
    public function getRule($request){
        $action = $request->action;
        $resource_id = $request->getParam('id');
        $rule = 'noAction';
    
        switch($action){
            case 'tag': $rule = array('privilege'=> 'tag','work_id' => $resource_id);      
                        break; 
            case 'deletetag':
                        $rule = array('privilege'=> 'read','work_id' => $resource_id);       
                        break; 
        }               
        return $rule;        
    }
    
    
}
