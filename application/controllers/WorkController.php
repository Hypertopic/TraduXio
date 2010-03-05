<?php

/**
 * Work controller
 *
 * 
 * @uses       Tdxio_Controller_Abstract
 * @package    Traduxio
 * @subpackage Controller
 */ 
 
class WorkController extends Tdxio_Controller_Abstract
{
	protected $_modelname='Work'; 
	
	public function init()
	{
		// Local to this controller only; affects all actions,
		// as loaded in init: 
	}

	    /**
	     * The index, or landing, action will be concerned with listing the entries 
	     * that already exist.
	     *
	     * Assuming the default route and default router, this action is dispatched 
	     * via the following urls:
	     * - /work/
	     * - /work/index
	     *
	     * @return void
	     */
	public function indexAction()
	{
		$work = $this->getModel();
		$entries = $work->fetchAllOriginalWorks();
		Tdxio_Log::info($entries);
		
		$sort=array();
        $langs=array();
        $authors=array(-1=>array('name'=>'No Author'));
		if(!is_null($entries)){
			foreach ($entries as $entry) {
				if (isset($entry['language'])) {
					$lang=$entry['language'];
					if (!isset($sort[$lang])) {
						$sort[$lang]=array();
					}
					if (!isset($langs[$lang])) {
//						$langs[$lang]=$entry['Language'];
						$langs[$lang]=$entry['language'];

					}
					if (!isset($entry['author']) || $entry['author']==='' /*|| $entry['Author']['name']===''*/) {
						$entry['author']=-1;
					}
						$author=$entry['author'];
						if (!isset($sort[$lang][$author])) {
							$sort[$lang][$author]=array();
						}
						if (!isset($authors[$author])) {
							//$authors[$author]=$entry['Author'];
							$authors[$author]=$entry['author'];
							
						}
						$sort[$lang][$author][]=$entry;
				}
			}
		}
        $this->view->entries=$sort;
        $this->view->langs=$langs;
        $this->view->authors=$authors;
        Tdxio_Log::info($langs);
        Tdxio_Log::info($authors);
        Tdxio_Log::info($sort);
        
	}
		
		
	public function depositAction()
	{		
		$form = new Form_TextDeposit();

		if ($this->getRequest()->isPost()) {
		    
			if ($form->isValid($this->getRequest()->getPost())) {
				
				$data = $form->getValues();
				$data['creator']=Tdxio_Auth::getUserName();
				$model = $this->getModel();
				$model->save($data);
				Tdxio_Log::info($data);
				return $this->_helper->redirector('index');
			}
		}
		$this->view->form = $form;
	} 
	
	public function readAction(){
	
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$model = $this->getModel();
		$tagForm = new Form_Tag();
		
		if (!$id || !($work=$model->fetchOriginalWork($id))) {
			throw new Zend_Controller_Action_Exception(sprintf('Work Id "%d" does not exist.', $id), 404);
		}	
		Tdxio_Log::info('mostra il work: ');
		Tdxio_Log::info($work);
		if(empty($work['Sentences'])){
			return $this->_helper->redirector->gotoSimple('read','translation',null,array('id'=>$id));
		}
		
		if ($this->getRequest()->isPost()) {
		    
			if ($tagForm->isValid($this->getRequest()->getPost())) {
				
				$data = $tagForm->getValues();
				Tdxio_Log::info('dati di tag');
				Tdxio_Log::info($data);
				$this->tag($id,$data);
				return $this->_helper->redirector->gotoSimple('read','work',null,array('id'=>$id));
			}
		}
		
		
		$this->view->work = $work;
		$this->view->tagForm = $tagForm;
	}
	
	public function translateAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$model = $this->getModel();		
		if (!$id || !$origWork=$model->fetchOriginalWork($id)) {
		    throw new Zend_Controller_Action_Exception(sprintf('Work Id "%d" does not exist.', $id), 404);
		}
		$form = new Form_Translate();
		
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				$data=$form->getValues();
				$userid = Tdxio_Auth::getUserName();				
				$data['creator']=$userid;
				$newId=$model->createTranslation($data,$id);
				return $this->_helper->redirector->gotoSimple('edit','translation',null,array('id'=>$newId));
			}
		}
		$this->view->form=$form;
		$this->view->origWork=$origWork;
	}
		
	public function myAction(){
		$user = Tdxio_Auth::getUserName(); 
		if(!is_null($user)){
			$work = $this->getModel();
			$this->view->myEntries = $work->fetchMyTranslations($user);
			Tdxio_Log::info($this->view->myEntries);		
		}else return $this->_helper->redirector->gotoSimple('index','work');
	}
	
	public function extendAction(){
    	$request = $this->getRequest();
		$id=$request->getParam('id');
		
		$model=$this->_getModel(); 
		
		if (!$id || !($work=$model->fetchWork($id))) {
			throw new Zend_Controller_Action_Exception(sprintf('Work Id "%d" does not exist.', $id), 404);
		}	
		
		if(!$model->isOriginalWork($id)) {
			throw new Zend_Controller_Action_Exception(sprintf('Cannot extend a translation. Edit it instead.'), 404);
		}
		
		$sentenceModel = new Model_Sentence();
		
		if($id && $work=$model->fetchOriginalWork($id))
		{			
			$form = new Form_TextExtend(); 
			$lastsentence = $sentenceModel->fetchSentence($id,$sentenceModel->getLastSentenceNumber($id));
			Tdxio_Log::info($lastsentence,'pipipopo');
			$lasttext = ' '.$lastsentence[0]['content'];
			
			if ($this->getRequest()->isPost()) 
			{
				if ($form->isValid($request->getPost())){
					$model = $this->_getModel();
					$data=$form->getValues();
									
					unset($data['submit']);
					
					$newId=$model->update($data,$id);
				    return $this->_helper->redirector->gotoSimple('read',null,null, array('id'=>$id));
				}
			}
			$this->view->form=$form;
			$this->view->text=$work;
			$this->view->lasttext=$lasttext;
			
		}else {
            throw new Zend_Exception("Couldn't find text $id");
        }
	}	
	
	protected function tag($work_id,$data){
		
		$model= $this->getModel();
		$user = Tdxio_Auth::getUserName();
		$tag = array('username'=> $user, 'work_id'=> $work_id, 'comment' => $data['tag_comment']);
		$model->tag($tag);
	}
	
	protected function tagSentence()
	{
		
	}
	
	protected function getModel()
	{
		return new Model_Work();
	}
	

	
	public function getRule($request){
		$action = $request->action;
		$resource_id = $request->getParam('id');
		$rule = 'noAction';
		Tdxio_Log::info($request,'request');
		Tdxio_Log::info($resource_id,'resource_id');
		if(!is_null($resource_id)){ 
			$visibility=$this->_getModel()->getAttribute($resource_id,'visibility');
			Tdxio_Log::info($visibility,'visibilita');
		}
		
		switch($action){
			case 'index': 
						$rule = array('privilege'=> 'read','work_id' => null);		
						break; 
			case 'deposit': 
						if($request->isPost()){
							$rule = array('privilege'=> 'create','work_id' => null );		
						}else{$rule = array('privilege'=> 'create','work_id' => null, 'notAllowed'=>true);} 
						break; 
			case 'translate':
				if($request->isPost()){
					$rule = array('privilege'=> 'translate','work_id' => $resource_id);
				}else{
					$rule = array('privilege'=> 'read','work_id' => $resource_id, 'notAllowed'=>true);
				}
			case 'read':
						$rule = array('privilege'=> 'read','work_id' => $resource_id,'visibility'=>$visibility,'edit_privilege'=> 'edit');		
						break; 						
//			case 'edit':
//					if($request->isPost()){
//						$rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);		
//					}else{$rule = array('privilege'=> 'edit','work_id' => $resource_id,'notAllowed'=>true,'visibility'=>$visibility);		
//					} break; 
			case 'my': break;					
			case 'extend':
					if($request->isPost()){
						$rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);		
					}else{
						$rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility, 'notAllowed'=>true);	
					} break; 
			default:$rule = 'noAction';
		}				
		return $rule;
		
	}
	
	
}
