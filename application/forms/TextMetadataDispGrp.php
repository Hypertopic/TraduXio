<?php
// application/forms/TextMetadataDispGrp.php
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
 * This is the text deposit form.  It is in its own directory in the application
 * structure because it represents a "composite asset" in your application.  By
 * "composite", it is meant that the form encompasses several aspects of the
 * application: it handles part of the display logic (view), it also handles
 * validation and filtering (controller and model).
 */

class Form_TextMetadataDispGrp extends Zend_Form_DisplayGroup {
    //static protected $metadataElements=array('book_content'=>'Book Name','period_content'=>'Period','genre_content'=>'Genre','author_content'=>'Author','release'=>'Release Date');

    function init() {

        foreach ($this->_metadataElements() as $field=>$label) {
            $this->addElement('text',$field, array(
                'label' => __($label),
                'attribs' => array('class' => 'autocomplete inline')
            ));
            $elem=$this->getElement($field);
            $elem->addDecorator(array('Inline'=>'HtmlTag'),array('tag'=>'span','class'=>'inline tag'));
        }

    }

    static function apply (Zend_Form $form,$context=null,$tabindex=1) {
        $metadatas=self::_metadataElements($context);
        if ($metadatas) {
            foreach ($metadatas as $field=>$options) {
                $options['decorators']=array('ViewHelper','Label',array(array('Inline'=>'HtmlTag'),array('tag'=>'span','class'=>'inline tag')));
                $options['tabindex']=$tabindex++;
                $form->addElement('text',$field, $options);
                //$elem=$form->getElement($field.":");
                //$elem->addDecorator(array('Inline'=>'HtmlTag'),array('tag'=>'span','class'=>'inline tag'));
            }
            $form->addDisplayGroup(array_keys($metadatas),'metadata');
            $form->metadata->removeDecorator('DtDdWrapper');
            $form->metadata->removeDecorator('HtmlTag');
            $form->metadata->addDecorator('Fieldset');
        }
    }

    protected function _metadataElements($context=null) {
        static $elements=null;
        if (is_null($elements)) $elements=array();
        require_once APPLICATION_PATH . '/models/TextModel.php';
        $textModel=new TextModel();
        foreach ($textModel->getMetadata('category',$context) as $metadata=>$details) {
            if (!$details['noedit']) {
                $elements[$details['field']."_content"]=array('label'=>$metadata." : ",'attribs'=>array('class'=>'autocomplete'));
            }
        }
        foreach ($textModel->getMetadata('text',$context) as $metadata=>$field) {
            $elements[$field]=array('label'=>$metadata." : ");
        }
        return $elements;
    }
}
