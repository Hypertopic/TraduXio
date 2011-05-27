<?php
class Form_AjaxWorkTranslate extends Form_Abstract
{
	public function init()
    {	
		$this->setMethod('post');
		 

		$this->addElement('text', 'author', array(
            'label'      => __("Author"),
            'id' => 'translate-author',
            'required' => true,
            'decorators' => array(
                'ViewHelper',
                'Label',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array('Label', array('tag' => 'span','requiredSuffix'=>' : ','requiredPrefix'=>'*')),
                array(array('row'=>'HtmlTag'),array('tag'=>'div','class'=>'tr-inputline'))
            )
        ));
        
		$this->addElement('text', 'translator', array(
			'label'      => __("Translator"),
			'id' => 'translate-translator',
			'required' => true,
            'decorators' => array(
                'ViewHelper',
                'Label',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array('Label', array('tag' => 'span','requiredSuffix'=>' : ','requiredPrefix'=>'*')),
                array(array('row'=>'HtmlTag'),array('tag'=>'div','class'=>'tr-inputline'))
            )
		));
		
		$this->addElement('text', 'title', array(
			'label'      => __("Title"),
			'id' => 'translate-title',
			'required' => true,
            'decorators' => array(
                'ViewHelper',
                'Label',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array('Label', array('tag' => 'span','requiredSuffix'=>' : ','requiredPrefix'=>'*')),
                array(array('row'=>'HtmlTag'),array('tag'=>'div','class'=>'tr-inputline'))
            )
		));
        
        // language dropdown
        $this->addElement('select', 'language', array(
            'label'      => __('Translate to'),
            'multiOptions'=> $this->_getLanguages(),
            'required'   => true,
            'decorators' => array(
                'ViewHelper',
                'Label',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array('Label', array('tag' => 'span','requiredSuffix'=>' : ','requiredPrefix'=>'*')),
                array(array('row'=>'HtmlTag'),array('tag'=>'div','class'=>'tr-inputline'))
            )
        ));
		$view = new Zend_View;
		$baseUrl = $view->baseUrl();
    
		$this->addElement('image','closeimg',array(
			'class' => 'closeimg',
			'alt' => __('Close'),
			'value' => ($baseUrl.'/images/close16.png'),
			'title' => __('Close'),
			'decorators'=>array(
				'ViewHelper',
				array(array('data'=>'HtmlTag'), array('tag'=>'span')),
		)));
        
        $this->addElement('submit', 'submit', array(
            'label'    => __('Deposit'),
            'id'=> 'translate-submit',
            'decorators' => array(
                'ViewHelper',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array(array('row'=>'HtmlTag'),array('tag'=>'span'))
            )
        ));
        $this->addElement('reset', 'reset', array(
            'label'    => __('Reset'),
            'id'=> 'translate-reset',
            'decorators' => array(
                'ViewHelper',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array(array('row'=>'HtmlTag'),array('tag'=>'span'))
            )
        ));

        $this->setAttrib('id','translate-form');
    } 
    

}

