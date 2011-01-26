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
        $user = !is_null($user)?$user:Tdxio_Auth::getUserRole();
        $request = $this->getRequest();
        $taggable_id = $request->getParam('id');
        $genre = $request->getParam('genre');
        $tag = $request->getParam('tag');
        $data = array('username'=> $user, 'taggable_id'=> $taggable_id,'genre'=> $genre, 'comment' => $tag);
        $model->tag($data); 
        $histModel = new Model_History();
        
        Tdxio_Log::info('ADD HISTORY TAG');
        $histModel->addHistory($taggable_id,3,array('tag'=>$tag,'genre'=>$genre));            
        
        //$this->_redirect($_SERVER['HTTP_REFERER']);
    }
    public function ajaxtagAction(){
        
        $request=$this->getRequest();
        Tdxio_Log::info($request,'tagAction request');
        if ($request->isPost()) {
            $values=$request->getPost();
            $model= $this->_getModel();
            $user = Tdxio_Auth::getUserName();
            $user = !is_null($user)?$user:Tdxio_Auth::getUserRole();
            $params = $request->getParams();
            Tdxio_Log::info($params,'tagAction request params');
            $data = array('username'=> $user, 'taggable_id'=> $params['id'],'genre'=> $params['tag_genre'], 'comment' => $params['tag_comment']);
            $response = $model->tag($data);
            $tags = $model->getTags($params['id']);
            $this->view->response=$response;     
            
            if($response['outcome']==true){
                $histModel = new Model_History();
                Tdxio_Log::info('ADD HISTORY TAG');
                $histModel->addHistory( $params['id'],3,array('tag'=>$params['tag_comment'],'genre'=>$params['tag_genre']));
            }
        } else {
            Tdxio_Log('debug 1010');
            throw new Zend_Controller_Action_Exception('Incorrect query.', 500);
        }
    }
     
    public function deletetagAction(){      
        Tdxio_Log::info('entra in deletetagAction');
        $username = Tdxio_Auth::getUserName();  
        $request=$this->getRequest();
        $taggableId=$request->getParam('id');
        $tagId=$request->getParam('tagid');
        $genre=$request->getParam('genre');
        $model= $this->_getModel();
        $tag = $model->getTag($tagId);
        $rowsAffected = $model->deleteTag($username,$tagId);
        $this->view->response = ($rowsAffected>0);
        Tdxio_Log::info($rowsAffected,'rows affected');
        $result = $model->getTags($taggableId,$genre);
        Tdxio_Log::info(empty($result[$taggableId]),'esito');
        $this->view->last=empty($result[$taggableId]);
        
        if($rowsAffected>0){
            $histModel = new Model_History();
            $histModel->addHistory($taggableId,4,array('tag'=>$tag['comment'],'genre'=>$genre));   
        }
    }
    
    public function getRule($request){
        Tdxio_Log::info('Entra in tag-getRule');
        $action = $request->action;
        $resource_id = $request->getParam('id');
        
        if(!is_null($resource_id)){ 
            $taggableModel = new Model_Taggable();
            if(!($taggableModel->entryExists(array('id'=>$resource_id))))
            {throw new Zend_Exception(sprintf('Taggable Id "%d" does not exist.',$resource_id), 404);}
        }
        Tdxio_Log::info('supera controllo risorsa');
        
        $rule = 'noAction';
    
        switch($action){
            case 'tag': $rule = array('privilege'=> 'tag','work_id' => $resource_id);      
                        break; 
            case 'ajaxtag': $rule = array('privilege'=> 'tag','work_id' => $resource_id);      
                        break; 
            case 'deletetag':
                        $rule = array('privilege'=> 'read','work_id' => $resource_id);       
                        break; 
        }               
        return $rule;        
    }
    
        
    
}
