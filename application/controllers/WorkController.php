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
		//return $this->_helper->redirector('sign','work');
        $work = new Model_Work();
        $this->view->entries = $work->fetchAll();
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
				$data['author']=Tdxio_Auth::getUserName();
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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     *
     * Assuming the default route and default router, this action is dispatched 
     * via the following url:
     * - /work/sign
     *
     * @return void
     */
    public function signAction()
    {
        $request = $this->getRequest();
        $form    = new Form_Work();

        // Check to see if this action has been POST'ed to.
        if ($this->getRequest()->isPost()) {
            
            // Now check to see if the form submitted exists, and
            // if the values passed in are valid for this form.
            if ($form->isValid($request->getPost())) {
                
                // Since we now know the form validated, we can now
                // start integrating that data sumitted via the form
                // into our model:
                $model = new Model_Work();
				$data = $form->getValues();
                $model->save($data);
                
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
	
	
	protected function getModel()
	{
	
		
		return new Model_Work();
	}
}
