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
		$this->view->entries = $work->fetchAllOriginalWorks();
		Tdxio_Log::info($this->view->entries);
	}
		
		
	public function depositAction()
	{
		
		$form = new Form_TextDeposit();

		// Check to see if this action has been POST'ed to.
		if ($this->getRequest()->isPost()) {
		    
			// Now check to see if the form submitted exists, and
			// if the values passed in are valid for this form.
			if ($form->isValid($this->getRequest()->getPost())) {
				
				// Since we now know the form validated, we can now
				// start integrating that data sumitted via the form
				// into our model:
				$data = $form->getValues();
				$data['creator']=Tdxio_Auth::getUserName();
				$model = $this->getModel();
				$model->save($data);
				Tdxio_Log::info($data);
				// Now that we have saved our model, lets url redirect
				// to a new location.
				// This is also considered a "redirect after post";
				// @see http://en.wikipedia.org/wiki/Post/Redirect/Get
				return $this->_helper->redirector('index');
			}
		}
		
		// Assign the form to the view
		$this->view->form = $form;
	} 
	
	public function readAction(){
	
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$model = $this->getModel();
		
		if (!$id || !($work=$model->fetchOriginalWork($id))) {
			throw new Zend_Controller_Action_Exception(sprintf('Work Id "%d" does not exist.', $id), 404);
		}	
		Tdxio_Log::info('mostra il work: ');
		Tdxio_Log::info($work);
		if(empty($work['Sentences'])){
			return $this->_helper->redirector->gotoSimple('read','translation',null,array('id'=>$id));
		}
		$this->view->work = $work;
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
		
	protected function getModel()
	{
		return new Model_Work();
	}
	
	
}
