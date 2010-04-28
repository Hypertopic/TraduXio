<?php
// application/forms/WorkEdit.php
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

class Form_WorkEdit extends Form_Abstract
{

    protected $_workId=null;
    
    function __construct($id) {
        $this->_workId=$id;
        parent::__construct();
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

        $attrs = $this->_getAttributes();
        $this->addElement('text','title',array(
            'value' =>  $attrs['title'],
            'label' =>  __('Title'),
            'decorators' => array('ViewHelper',array('HtmlTag',array('tag'=>'span')),'Label'),
            
            //array('ViewHelper','Label',array(array('Inline'=>'HtmlTag'),array('tag'=>'span','class'=>'inline tag')));
            ));
            
        $this->addElement('text','author',array(
            'value' =>  $attrs['author'],
            'label' =>  __('Author'),
            'decorators' => array('ViewHelper',array('HtmlTag',array('tag'=>'span')),'Label'),
            ));
            
            
        $this->addDisplayGroup(array('title','author'),'tit_auth');
        $this->tit_auth->setDecorators(array('FormElements',array('HtmlTag',array('tag'=>'div', 'class'=>'spaced'))));
        
        $this->addElement('select', 'language', array(
            'label'      => __('Language'),
            'multiOptions'=> $this->_getLanguages(),
            'value'     => $attrs['language'],
            'decorators' => array('ViewHelper',array('HtmlTag',array('tag'=>'span')),'Label'),
        ));
        
        $this->addDisplayGroup(array('language'),'lang');
        $this->lang->setDecorators(array('FormElements',array('HtmlTag',array('tag'=>'div', 'class'=>'spaced'))));
        
        // add the submit button
        $this->addElement('submit', 'submit', array(
            'label'    => __('Save'),
            'decorators' => array('ViewHelper',array('HtmlTag',array('tag'=>'span'))),
        ));

        $this->addElement('submit', 'cancel', array(
            'label'    => __('Cancel'),
            'decorators' => array('ViewHelper',array('HtmlTag',array('tag'=>'span'))),
        ));
        
        $this->addDisplayGroup(array('submit','cancel'),'buttons');
        $this->buttons->setDecorators(array('FormElements',array('HtmlTag',array('tag'=>'div', 'class'=>'spaced'))));


    }

    protected function _getAttributes()
    {
        $workModel = new Model_Work();
        $data = $workModel->getAttribute($this->_workId,array('author','title','language'));
        return $data;
    }

}

