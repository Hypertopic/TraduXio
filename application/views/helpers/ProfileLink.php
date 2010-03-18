<?php

class Zend_View_Helper_ProfileLink extends Zend_View_Helper_Abstract
{
    function profileLink()
    {
        $username = Tdxio_Auth::getUserName();
        if(null!==$username){
            return __('Welcome').', ' . $username.' (<a href="'.$this->view->makeUrl('/login/logout').'">'.__('Logout').'</a>)';
        }
        return '<a href="'.$this->view->makeUrl('/login').'">'.__('Login').'</a>';
    }
    
}
