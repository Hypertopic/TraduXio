<?php

/**
 * This is the work form.  It is in its own directory in the application 
 * structure because it represents a "composite asset" in your application.  By 
 * "composite", it is meant that the form encompasses several aspects of the 
 * application: it handles part of the display logic (view), it also handles 
 * validation and filtering (controller and model).  
 *
 * @uses       Zend_Form
 * @package    Traduxio
 * @subpackage Form
 */
class Form_Work extends Zend_Form
{
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
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add an email element
        $this->addElement('text', 'title', array(
            'label'      => 'Title:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            // 'validators' => array(
                // 'WorkTitle',
            // )
        ));

        $this->addElement('text', 'author', array(
            'label'      => 'Author:',
            'required'   => true,
        ));

		$this->addElement('text', 'language', array(
            'label'      => 'Language:',
            'required'   => true,
        ));


        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Insert Work',
        ));

        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }
}
