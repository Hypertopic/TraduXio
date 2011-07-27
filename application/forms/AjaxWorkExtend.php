<?php
class Form_AjaxWorkExtend extends Form_Abstract
{
public function init()
    {
        // set the method for the display form to POST
        $this->setMethod('post');
        //$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $this->addElement('textarea', 'extendtext', array(
            'required'   => true,
            'label' => __('Text to add'),
            'rows' => 10,
            'id' => 'insert-text',
            'decorators' => array(				
                array('Label', array(/*'tag' => 'span',*/'requiredSuffix'=>' : ','requiredPrefix'=>'*')),
				'ViewHelper',
				'Errors'				
			)
        ));
    
    
        $this->addElement('submit', 'extendsubmit', array(
            'label'    => __('Deposit'),
            'id'=> 'extend-submit',
            'decorators' => array(
                'ViewHelper',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array(array('row'=>'HtmlTag'),array('tag'=>'span'))
            )
        ));
        $view = new Zend_View;
		$baseUrl = $view->baseUrl();    
        Tdxio_Log::info($baseUrl,'base url in form');

        $this->addElement('image','closeimg',array(
			'class' => 'closeimg',
			'alt' => __('Close'),
			'title' => __('Close'),
			'value' => ($baseUrl.'/images/close16.png'),			
			'decorators'=>array(
			'ViewHelper',
			array(array('data'=>'HtmlTag'), array('tag'=>'span')),
        )));
        
        $this->addElement('reset', 'resetbtn', array(
            'label'    => __('Cancel'),
            'id'=> 'extend-cancel',
            'decorators' => array(
                'ViewHelper',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array(array('row'=>'HtmlTag'),array('tag'=>'span'))
            )
        ));

        $this->setAttrib('id','extend-form');
    }

}

