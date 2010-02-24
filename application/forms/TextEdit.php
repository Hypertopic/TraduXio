<?php
// application/forms/TextEdit.php
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

class Form_TextEdit extends Form_Abstract
{

    protected $_type=null;
    function __construct($type=null) {
        $this->_type=$type;
        $this->log("creating text edit form for $type");
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

        /*$title=$this->createElement('text','title');
        //var_dump($title);
        $title->setOptions(array(
            'label'      => 'Title',
            'required'   => true
        ));
        $title->setAttrib('class','title');
        $title->setAttrib('class',$title->getAttrib('class').' pouet');
        $this->addElement($title);*/

        require_once "TextMetadataDispGrp.php";
        TextMetadataDispGrp::apply($this,$this->_type);

        // add the submit button
        $this->addElement('submit', 'submit', array(
            'label'    => __('Save'),
        ));

    }



    protected function _getLanguages()
    {
        static $languageOptions=null;

        if (null === $languagesOptions) {
            require_once APPLICATION_PATH . '/models/LanguageModel.php';
            $langModel = new LanguageModel();
            $languageOptions=$langModel->fetchOptions();
        }
        return $languageOptions;
    }

}

