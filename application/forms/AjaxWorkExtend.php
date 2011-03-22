<?php
class Form_AjaxWorkExtend extends Form_Abstract
{
public function init()
    {
        // set the method for the display form to POST
        $this->setMethod('post');
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $this->addElement('textarea', 'extendtext', array(
            'required'   => true,
            'rows' => 10,
            'class' => 'autogrow mini',
            'cols'=>150,
            'id' => 'extend-text'
        ));

        $this->addElement('submit', 'submit', array(
            'label'    => __('Deposit'),
            'id'=> 'extend-submit'
        ));

        $this->setAttrib('id','extend-form');
    }

}

