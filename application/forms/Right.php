<?php
// application/forms/Right.php

class Form_Right extends Form_Abstract
{
    public function init()
    {
        $this->setMethod('post');
        $role = Tdxio_Auth::getUserRole();
        
        $title=$this->createElement('text','url');
        $title->setOptions(array(
            'decorators' => array('ViewHelper',
                                'Description',
                                'Label',
                                'Errors',
                                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                                array('Label', array('tag' => 'span','requiredSuffix'=>' : ')),
                                array(array('row'=>'HtmlTag'),array('tag'=>'div','class'=>'input-line'))),
            'label'      => __("at the page"),
            'required'   => true
        ));

        $this->addElement($title);

        $check_error = $this->createElement('checkbox','error');
        $check_error->setLabel(__("Bad or missing reference (author, editor, license)"));
        $check_error->setDecorators(array(
                   'ViewHelper',
                   'Description',
                   'Errors',
                   array(array('data'=>'HtmlTag'), array('tag' => 'span')),
                   array('Label', array('tag' => 'span','placement'=>'APPEND')),
                   array(array('row'=>'HtmlTag'),array('tag'=>'dl','class'=>'checkbox'))
           ));
        $this->addElement($check_error);    
        
        $check_abuse = $this->createElement('checkbox','abuse');
        $check_abuse->setLabel(__("Publication of protected work non-comparable to a short quotation"));
        $check_abuse->setDecorators(array(
                   'ViewHelper',
                   'Description',
                   'Errors',
                   array(array('data'=>'HtmlTag'), array('tag' => 'span')),
                   array('Label', array('tag' => 'span','placement'=>'APPEND')),
                   array(array('row'=>'HtmlTag'),array('tag'=>'dl','class'=>'checkbox'))
           ));

        $this->addElement($check_abuse);    
        
        //$this->addDisplayGroup(array('error','abuse'), 'check-group');
           
      //$this->setElementDecorators(array(),array('check-group'));
        
        $details = $this->createElement('textarea', 'body', array(
            'decorators' => array(
                'ViewHelper', 
                array('HtmlTag',array('tag'=>'div')),
                array('Label',array('optionalSuffix'=>" : ",'tag'=>'div'))),
            'label'      => __('Details (optional)'),
            'required'   => false,
            'rows' => 6,
            'cols'=>80
        ));

        $this->addElement($details);
    
        if($role=="guest"){
            $this->addElement('captcha','captcha',array(
                'label' => __("Anti-spam system"),
                'decorators' => array(
                    array('Label',array('requiredSuffix'=>" : ")),
                    array('Errors', array('placement'=>'APPEND')),
                    array('HtmlTag',array('tag'=>'div'))),
                'captcha' => array(
                'captcha' => 'Figlet',
                'wordLen' => 4,
                'timeout' => 300,
            )));
        }
        
        $email=$this->createElement('text','emailaddress',array(
            'decorators' => array('ViewHelper','Errors','Description',array('HtmlTag',array('tag'=>'div')),array('Label',array('optionalSuffix'=>" : "))),
            'label'=>__("Email address (optional)"),
            'required'=> false
        ));
                
        $email->addValidator(new Zend_Validate_EmailAddress());
        
        $this->addElement($email);
        
        if($role=="member"){
            $username = Tdxio_Auth::getUserName();
            $userhidden = $this->createElement('hidden','username',array('value'=>$username));
            $userhidden->removeDecorator( 'Label' );
            $this->addElement($userhidden);
            $email->setOptions(array('required'=>false));
        }
        
        // add the submit button
        $this->addElement('submit', 'submit', array(
            'label'    => __('Report'),
            'decorators'=> array('ViewHelper',array('HtmlTag',array('tag'=>'div','id'=>'abuse-submit')))
        ));  
        
        $this->setAttrib('class','send-form');
        
        
    }

}

