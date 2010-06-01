<?php 
// application/forms/AddPrivilege.php 
/***
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 **/

/**
 * This is the text edit form.  It is in its own directory in the application
 * structure because it represents a "composite asset" in your application.  By
 * "composite", it is meant that the form encompasses several aspects of the
 * application: it handles part of the display logic (view), it also handles
 * validation and filtering (controller and model).
 */

class Form_AddPrivilege extends Form_Abstract
{

    public $_privilegeList=null;
    
    function __construct() {
        $privModel = new Model_Privilege();
        $this->_privilegeList = $privModel->_dbPrivilegeList;
        return parent::__construct();
    }
    /**
     * init() is the initialization routine called when Zend_Form objects are
     * created. In most cases, it make alot of sense to put definitions in this
     * method, as you can see below.  This is not required, but suggested.
     * There might exist other application scenarios where one might want to
     * configure their form objects in a different way, those are best
     * described in the manual:
     *
     * @see    http://framework.zend.com/manual/en/zend.form.html
     * @return void
     */
    public function init()
    {
        // set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('class','privilege-form');
        $this->setAttrib('id','addform');
        
        $this->addElement('select', 'user', array(
            'label'      => __('User'),
            'multiOptions'=> $this->_getUsers(),
            'id'   =>  'usersel',
        //'required' => true,
            'class' => 'manage-select'
        ));
        
        $this->addElement('select', 'privilege', array(
        'label'      => __('Privilege'),
        'multiOptions'=> $this->_privilegeList,
        'id'   =>  'privsel',
        //'required' => true,
        'class' => 'manage-select'
        ));             
        
        // add the submit button
        $this->addElement('submit', 'submit', array(
            'label'    => __('Add Privilege'),
            'id' => 'addsubmit'
        ));
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }

    protected function _getUsers()
    {
        $userModel = new Model_User();
        $usersarray=$userModel->fetchAll();
        Tdxio_Log::info('usersarray in addprivilege');
        Tdxio_Log::info($usersarray->toArray());
        $users['all']=__('All users');// All users, included unnregistered ones (guests)
        $users['member']=__('All members');// All registered users
        foreach($usersarray as $key=>$user){
            $users[$user['name']]=$user['name'];
        }       
        Tdxio_Log::info($users,'utenti registrati in db');
        return $users;
    }
    
    public function isValid($_POST){
        $validParent = parent::isValid($_POST);
        $valid = true;
        $elements = $this->getElements();
        if($elements['user']->getValue() =='all')
        {           
            $action = $elements['privilege']->getValue();
            if( $action != 'read')//if the privilege is not a read privilege
            {   
                $this->setDescription(__("Privilege not added: you can't grant creation/edition privileges to unregistered users."));
                $valid = false;
            }
        }
        $valid = $valid and $validParent; 
        return ($valid);
    }
}

