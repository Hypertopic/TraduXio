<?php

class Form_LangSel extends Zend_Form
{
    public $_langs = false;
    
    public function init()
    {
        Tdxio_Log::info('flusso: 12 FORM INIT');
        
        $this->_langs=Zend_Registry::get('languages');
        asort($this->_langs);
        
        Tdxio_Log::info($this->_langs,' langs');
                           
        $this->setMethod('post');
        
        
        $lang = $this->createElement('select','lang', array(
            'decorators' => array('FormElements','ViewHelper'),
            'multiOptions'=> $this->_langs,
            'id'   =>  'langsel',
            'onChange' => 'this.form.submit();',
            'value' => $this->_getPrefLang(),
            'required'=>true
        ));
        $this->addElement($lang);        
        //Tdxio_Log::info($this,'decorators');
        $this->setDecorators(array('FormElements',
                            array('HtmlTag', array('tag' => 'span')),
                           array('Form',array('class'=>'inline'))
                           ));
        
    }
    
    public function _getPrefLang(){
        Tdxio_Log::info('flusso: 13 FORM GETPREFLANG');
       
       /* $prefs = Tdxio_Preferences::getPref('lang');        
        
        if(isset($prefs['lang'])){  
            Tdxio_Log::info($prefs['lang'],'lingua preferita nel select');
            $prefLang = $prefs['lang'];
        }
        Tdxio_Log::info($prefLang,'sel pref lang');
        return $prefLang;*/
        return Tdxio_Preferences::getCurLanguage();    
    }
}
