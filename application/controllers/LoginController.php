<?php 

/*** Login controller
 *
 * 
 * @uses       Zend_Controller_Action
 * @package    Traduxio
 * @subpackage Controller
 */
 
class LoginController extends Zend_Controller_Action
{
    
	public function preDispatch()
	{
		if (Zend_Auth::getInstance()->hasIdentity()) {
			// If the user is logged in, we don't want to show the login form;
			// however, the logout action should still be available
			if ('logout' != $this->getRequest()->getActionName()) {
			$this->_helper->redirector('index', 'index');
			}
		} else {
			// If they aren't, they can't logout, so that action should
			// redirect to the login form
			if ('logout' == $this->getRequest()->getActionName()) {
				$this->_helper->redirector('index');
			}
		}
	}
	
	public function indexAction()
	{
		$this->view->form = $this->getForm();
	}
	
	
	public function processAction()
	{
		$request = $this->getRequest();

		// Check if we have a POST request
		if (!$request->isPost()) {
		    return $this->_helper->redirector('index');
		}

		// Get our form and validate it
		$form = $this->getForm();
		if (!$form->isValid($request->getPost())) {
		    // Invalid entries
		    $this->view->form = $form;
		    return $this->render('index'); // re-render the login form
		}

		// Get our authentication adapter and check credentials
		$adapter = $this->getAuthAdapter($form->getValues());
		$auth    = Zend_Auth::getInstance();
		$result  = $auth->authenticate($adapter);
		// $this->log($result);
		if (!$result->isValid()) {
			// Invalid credentials
			$form->setDescription('Invalid credentials provided');
			$this->view->form = $form;
			return $this->render('index'); // re-render the login form
		}
		$user = Tdxio_Auth::getUserName();
		$model = new Model_User();			
		$model->registerUser($user);
			
		// We're authenticated! Redirect to the home page
		$this->_helper->redirector('index', 'index');
	}
	
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index','index'); // back to login page
    }
	
	public function getForm()
    {
		return new Form_Login(array(
            'action' => $this->view->makeUrl('/login/process'),
            'method' => 'post',
        ));
	}
	
	 public function getAuthAdapter(array $params)
    {
        $options=array(array(
            'host'=>'ldap.hypertopic.org'
            //'accountDomainName'=>'',
        ));
        //$this->log($params);
        return new Zend_Auth_Adapter_Ldap($options, "cn=".$params['username'].",dc=hypertopic,dc=org",
                                      $params['password']);        // Leaving this to the developer...
        // Makes the assumption that the constructor takes an array of
        // parameters which it then uses as credentials to verify identity.
        // Our form, of course, will just pass the parameters 'username'
        // and 'password'.
    }
    
	
}
