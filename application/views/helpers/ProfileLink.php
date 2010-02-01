<?php

class Zend_View_Helper_ProfileLink extends Zend_View_Helper_Abstract
{
    function profileLink()
    {
		$username = Tdxio_Auth::getUserName();
		if(null!==$username){
			return 'Welcome, ' . $username.' (<a href="'.$this->view->makeUrl('/login/logout').'">Logout</a>)';
        }
        return '<a href="'.$this->view->makeUrl('/login').'">Login</a>';
    }
	
}
