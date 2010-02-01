<?php

require_once APPLICATION_PATH.'/models/PrivilegeModel.php'; 

class Tdxio_Plugin_AclPlugin extends Zend_Controller_Plugin_Abstract
{
//    private $_acl;
    public $_userid;
	public $_role;

    private $_noauth = array('controller' => 'login',
                             'action' => 'index');

    private $_noacl = array('controller' => 'error',
                            'action' => 'denied');
   
    public function __construct()
    {
   		$aclHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('TdxioAuthHelper');
		$this->_userid = $aclHelper->getUserId();
		$this->_role = $aclHelper->getRole();
    }

	public function preDispatch($request)
	{
		$controllername = $request->getControllerName();
		$controllername[0]=strtoupper($controllername[0]);
		$actionName = $request->getActionName();
		
		//If the request is not for a controller we can just exit here without doing anything
		if(empty($controllername))
			return;
			
		$response = $this->getResponse();
		$classname = $controllername.'Controller';
		if (!file_exists(APPLICATION_PATH . '/controllers/'.$classname.'.php')) return;

		require_once APPLICATION_PATH . '/controllers/'.$classname.'.php';
		$controller = new $classname($request,$response); 
		
		$rule = $controller->getRule($request);
		$this->log($rule," rule dopo text-controller");
		
		$layout = Zend_Controller_Action_HelperBroker::getStaticHelper('Layout');
		$view = $layout->getView();
		$view->userid=$this->_userid;
		$view->preview = false;//view even unaccessible content
		$privilegeModel = new PrivilegeModel();
		$create_privilege = array(
					'user_id' => $this->_userid,
					'role' => $this->_role,
					'privilege'=> 'create',
					'text_id' => null
		);	
		if ($privilegeModel->exist($create_privilege) || $view->preview){$view->showCreate=true;}
		else {$view->showCreate=false;}
		
		if($rule == 'noAction')
		{return;}
		
		$privilege = array(
					'user_id' => $this->_userid,
					'role' => $this->_role,
					'privilege'=> $rule['privilege'],
					'text_id' => $rule['text_id'],
					'visibility' => $rule['visibility']
		);
		$this->log($privilege,'privilegio');
		$view->showEdit=false;
		
		if (!($privilegeModel->exist($privilege))){
			$this->log('not exist');
			if(isset($rule['notAllowed'])&&$view->preview){
				$view->notAllowed=true;
				return;
			}
			if ($this->_role=='guest'){
				$controllername = $this->_noauth['controller'];
				$actionName = $this->_noauth['action'];
			} else {
				$controllername = $this->_noacl['controller'];
				$actionName = $this->_noacl['action'];
			}
		}else{
			if(isset($rule['edit_privilege'])){
				$editPrivilege=$privilege;
				$editPrivilege['privilege']=$rule['edit_privilege'];
				if(($privilegeModel->exist($editPrivilege))||$view->preview){
					$view->showEdit=true;
				}
			}				
			$view->notAllowed=false;
		}
		
		
		$request->setControllerName($controllername);
		$request->setActionName($actionName);
	}
	
	public function getUserId(Zend_Auth $auth=null)
    {
		if(is_null($auth)){
			$auth = Zend_Auth::getInstance();
		}
        if ($auth->hasIdentity()) {
            $username = $auth->getIdentity();
           if (preg_match('/cn\=([a-zA-Z0-9.]*)/',$username,$matches)) {
				$this->_userid=$matches[1];
                return $matches[1];
           }
		}
	
		return null;
    }
	
/* 	public function getEntriesByUserId($userid){
		if (userid=='diana.zambon'){
			$test_entries=array('ita' => array(
												5 => array(
															'id'=>175,
															'title'=>'creatore',
															'Translations'=>array(
																				  176,
																				  177)
															)
												)
								);
								
			return $test_entries;
		}
		return null;
	}
 */	
	protected $_logger=null;
    protected function log($message='',$title=null,$priority=1) {
        if (!$this->_logger) {
            global $logger;
            $this->_logger=$logger;
        }
        if (is_null($message)) {
            $message="{NULL}";
        } elseif (is_bool($message)) {
            $message="{".($message ? 'TRUE':'FALSE')."}";
        } elseif (is_array($message) || is_object($message)) {
            $message=print_r($message,true);
        }
        if (null !== $title) $message="[$title] : ".$message;
        $this->_logger->log($message,$priority);
    }

}
