<?php

//require_once APPLICATION_PATH.'/models/PrivilegeModel.php'; 

class Tdxio_Plugin_AclPlugin extends Zend_Controller_Plugin_Abstract
{
    public $_userid;
    public $_role;

    private $_noauth = array('controller' => 'login',
                             'action' => 'index');

    private $_noacl = array('controller' => 'error',
                            'action' => 'denied');
   
    public function __construct()
    {
        $this->_userid = Tdxio_Auth::getUserName();
        $this->_role = Tdxio_Auth::getUserRole();
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
        Tdxio_Log::info($rule," rule dopo text-controller");
        
        $layout = Zend_Controller_Action_HelperBroker::getStaticHelper('Layout');
        $view = $layout->getView();
        $view->userid=$this->_userid;
        $privilegeModel = new Model_Privilege();
        
        if($this->_role=='member'){
            $view->isMember=true;
        }else{$view->isMember=false;}
        
        Tdxio_Log::info($rule,'regola');
        if($rule == 'noAction')
        {return;}
                
        $privilege = array(
                    'user_id' => (($this->_userid != null)?$this->_userid:$this->_role),
                    'role' => $this->_role,
                    'privilege'=> $rule['privilege'],
                    'work_id' => $rule['work_id'],
                    'visibility' => (array_key_exists('visibility',$rule))?$rule['visibility']:null
        );
        Tdxio_Log::info($privilege,'privilegio');
        $view->showEdit=false;
        $view->showTranslate=false;
        
        if (!($privilegeModel->exist($privilege))){
            Tdxio_Log::info('the privilege does not exist');
 
            if ($this->_role=='guest'){                

               // $this->setLastRequestedUri($request);                        
                $controllername = $this->_noauth['controller'];
                $actionName = $this->_noauth['action'];
            } else {
                $controllername = $this->_noacl['controller'];
                $actionName = $this->_noacl['action'];
            }
        }else{
            Tdxio_Log::info('the privilege exists');
            if(isset($rule['edit_privilege'])){
                $editPrivilege=$privilege;
                $editPrivilege['privilege']=$rule['edit_privilege'];
                if(($privilegeModel->exist($editPrivilege))){
                    $view->showEdit=true;
                }
            }
            if(isset($rule['translate_privilege'])){
                $translatePrivilege=$privilege;
                $translatePrivilege['privilege']=$rule['translate_privilege'];
                if(($privilegeModel->exist($translatePrivilege))){
                    $view->showTranslate=true;
                }
            }     
        }
        
        $deletePrivilege = $privilege;
        $deletePrivilege['privilege']='delete';
        unset($deletePrivilege['visibility']);
        $view->canDelete=$privilegeModel->exist($deletePrivilege);
       
        $request->setControllerName($controllername);
        $request->setActionName($actionName);
    }
   /* 
    public function setLastRequestedUri($request){
        
        $requestUri = $request->getPathInfo();
        Tdxio_Log::info($requestUri);
        $session = new Zend_Session_Namespace('lastRequest');
        Tdxio_Log::info($requestUri,'REQUESTURIa');
        Tdxio_Log::info($request,'REQUESTURIa');
        Tdxio_Log::info($request->getParam('controller'),'controller');
        Tdxio_Log::info($request->getParam('action'),'action');     
        if(!(($request->getParam('controller')=='login')&&($request->getParam('action')=='index'))){
            $session->lastRequestUri = $requestUri;                    
        }   
        
    }*/
    
}
