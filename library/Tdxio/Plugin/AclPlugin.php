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
        $view->preview = false;//view even unaccessible content
        $privilegeModel = new Model_Privilege();
        $create_privilege = array(
                    'user_id' => $this->_userid,
                    'role' => $this->_role,
                    'privilege'=> 'create',
                    'work_id' => null
        );  
        if ($privilegeModel->exist($create_privilege) || $view->preview){$view->showCreate=true;}
        else {$view->showCreate=false;}
        if($this->_role=='member'){
            $view->isMember=true;
        }else{$view->isMember=false;}
        
        Tdxio_Log::info($rule,'regola');
        if($rule == 'noAction')
        {return;}
        
                
        $privilege = array(
                    'user_id' => $this->_userid,
                    'role' => $this->_role,
                    'privilege'=> $rule['privilege'],
                    'work_id' => $rule['work_id'],
                    'visibility' => $rule['visibility']
        );
        Tdxio_Log::info($privilege,'privilegio');
        $view->showEdit=false;
        
        if (!($privilegeModel->exist($privilege))){
            Tdxio_Log::info('the privilege does not exist');
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
            Tdxio_Log::info('the privilege exists');
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
    
}
