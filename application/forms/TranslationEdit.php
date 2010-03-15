<?php
// application/forms/TranslationEdit.php
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
 * This is the text translation form.  It is in its own directory in the application
 * structure because it represents a "composite asset" in your application.  By
 * "composite", it is meant that the form encompasses several aspects of the
 * application: it handles part of the display logic (view), it also handles
 * validation and filtering (controller and model).
 */
 
class Form_TranslationEdit extends Form_Abstract
{
    protected $_blocklist = null;
    protected $_taglist = null;

    public function __construct($blockList,$tagList)
    {
        $this->_blocklist=$blockList;
        $this->_taglist=$tagList;
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
        $tabindex=10;
        // set the method for the display form to POST
        $this->setMethod('post');

        // title element
        $this->addElement('text', 'title', array(
            //'label'      => __('Translated title'),
            'decorators' => array('ViewHelper',array('HtmlTag',array('tag'=>'div')),'Label'),
            'required'   => true,
            'tabindex'=>$tabindex++
        ));

     /*   if(!empty($this->_taglist)){
        foreach ( $this->_taglist as $key => $tag) {
            $tagElem=$this->createElement('submit','tag'.$tag['id'],array('label'=> 'X '.$tag['comment'],'decorators'=>array('ViewHelper')));
            $this->addElement($tagElem);
        }}
       */ 
        foreach ($this->_blocklist as $id=>$length) {
            $blockElem=$this->createElement('textarea', 'block'.$id,array('disableLoadDefaultDecorators'=>true));
            $blockElem->addDecorator('ViewHelper');
            $blockElem->setOptions(array(
                'class' => 'autogrow',
                'rows' => $length,
                'cols'=> '1',
                'tabindex'=>$tabindex++
            ));
            $this->addElement($blockElem);
        }
        

        // add the submit button
        $this->addElement('submit', 'submit', array(
            'label'    => __('Save'),'decorators'=>array('ViewHelper')
        ));
        $this->addElement('submit', 'submitquit', array(
            'label'    => __('Save and quit'),'decorators'=>array('ViewHelper')
        ));
        $this->addElement('submit', 'cancel', array(
            'label'    => __('Cancel'),'decorators'=>array('ViewHelper')
        ));

        $this->addDisplayGroup(array('submit','submitquit','cancel'),'buttons');
        $this->buttons->setDecorators(array('FormElements',array('HtmlTag',array('tag'=>'div'))));
       // $this->log($this->metadata->getDecorators(),'decorators');
    }

    public function blockList() {
        return array_keys($this->_blocklist);
    }

}
