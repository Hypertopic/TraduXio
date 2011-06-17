<?php
class Form_AjaxWorkSentencetag extends Form_Abstract
{	
/*	protected $__id;
	protected $__number;
	
	function __construct($id,$number) {	
		$this->__id = $id;
		$this->__number = $number;
        parent::__construct();
    }
  */  
	public function init()
    {	
		$this->setMethod('post');
		 
		$this->addElement('textarea','tag_comment', array(
            'id' => 'stagTA',
            'value' => __("Tag the sentence"),
            'required' => true,
            'decorators' => array(
                'ViewHelper',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array('Label', array('tag' => 'span','requiredSuffix'=>' : ','requiredPrefix'=>'*'))
            )
        ));
        		
        // sentences dropdown
   /*     $this->addElement('select', 'sentence', array(
            'multiOptions'=> $this->_getSentences($this->__id),
            'required'   => true,
            'width'=> '300px',
            'value' => $this->__number,
            'decorators' => array(
                'ViewHelper',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array('Label', array('tag' => 'span','requiredSuffix'=>' : ','requiredPrefix'=>'*')),
                array(array('row'=>'HtmlTag'),array('tag'=>'div','class'=>'tr-inputline'))
            )
        ));*/
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
            'label'    => __('Tag'),
            'id'=> 'stag-submit',
            'decorators' => array(
                'ViewHelper',
                'Errors',
                array(array('data'=>'HtmlTag'), array('tag'=>'span')),
                array(array('row'=>'HtmlTag'),array('tag'=>'span'))
            )
        ));

        $this->setAttrib('id','stag-form');
    } 
    
    
 /*   protected function _getSentences($id){
		$sentModel = new Model_Sentence();
		$sentences = $sentModel->fetchSentences($id);
		$list = array();
		foreach ($sentences as $k => $v) {
            $list[$v['number']] = $v['content'];
        }
		return $list;
	}
	*/	
    

}

