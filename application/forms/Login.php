<?php

class Form_Login extends Zend_Form
{
    public function init()
    { 
		
		/*$name->setOptions(array(
            'decorators' => array(
                'ViewHelper',
                'Label',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array('Label', array('tag' => 'span','requiredSuffix'=>' : ','requiredPrefix'=>'*')),
                array(array('row'=>'HtmlTag'),array('tag'=>'div','class'=>'input-line'))
            ),
            'label'      => __('Name'),
            'required'   => true
        ));*/
        $username = $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(),
            'required'   => true,
            'label'      => __('Your username').':',
			'decorators' => array(
                'ViewHelper',
                'Label',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array('Label', array('tag' => 'span','requiredSuffix'=>' : ','requiredPrefix'=>'*')),
                array(array('row'=>'HtmlTag'),array('tag'=>'div'))
            )
        ));

        $password = $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
            ),
            'required'   => true,
            'label'      => __('Password').':',
            'decorators' => array(
                'ViewHelper',
              //  'Label',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array('Label', array('tag' => 'span','requiredSuffix'=>' : ','requiredPrefix'=>'*')),
                array(array('row'=>'HtmlTag'),array('tag'=>'div'))
            )
        ));

        $password = $this->addElement('hidden', 'redirect', array(
            'validators' => array(
            ),
            'decorators' => array(
                'ViewHelper')
        ));

         $login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore'   => true,
            'label'    => __('Login'),
            'decorators' => array(
                'ViewHelper'
			)));

        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
}
