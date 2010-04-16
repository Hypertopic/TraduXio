<?php

class Form_LangSel extends Zend_Form
{
    public $_langs = false;
    
    public function init()
    {
        Tdxio_Log::info('flusso: 12 FORM INIT');
        
        $this->_langs=array('it'=>__('ita'),
                           'fr'=>__('fra'),
                           'en'=>__('eng'));
        asort($this->_langs);
        Tdxio_Log::info($this->_langs,' langs');
                           
        $this->setMethod('post');
        
        
        $lang = $this->createElement('select','lang', array(
            'decorators' => array('FormElements','ViewHelper'),
            //'label'      => __('Select Your Language'),
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
        $prefLang = 'en';
        
        $tdxioPrefs = new Zend_Session_Namespace('Tdxio_Prefs');
        $prefs = array();
        if(isset($tdxioPrefs->preferences)){
            $prefs = $tdxioPrefs->preferences;
            Tdxio_Log::info($prefs,'GETPREFLANG: session options exist');
        }
        
        /*try{
            $prefs = Zend_Registry::get('preferences');*/
            if(isset($prefs['lang'])){  
                Tdxio_Log::info($prefs['lang'],'lingua preferita nel select');
                $prefLang = $prefs['lang'];
           }/* 
        }catch(Zend_Exception $e){}*/
        Tdxio_Log::info($prefLang,'sel pref lang');
        return $prefLang;    
    }
}
