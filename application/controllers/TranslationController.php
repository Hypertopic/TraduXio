<?php 

/**
 * Translation controller
 *
 * 
 * @uses       Tdxio_Controller_Abstract
 * @package    Traduxio
 * @subpackage Controller
 */
 
class TranslationController extends Tdxio_Controller_Abstract
{
	protected $_modelname='Translation';
	
	public function init(){}
	
	public function editAction() {
		$request=$this->getRequest();
		$translationId=$request->getParam('id');
		$model= $this->_getModel();
		if(!$work=$model->fetchTranslationWork($translationId)){
			throw new Zend_Controller_Action_Exception(sprintf('Translation "%d" does not exist.', $translationId), 404);
		}
		$form = new Form_TranslationEdit($this->_getBlockList($work['TranslationBlocks']));
					
		if ($request->isPost()) {
			$post=$request->getPost();
			if (!isset($post['cancel'])) {
				if ($form->isValid($post)) {
					$data=$form->getValues();
					$data['TranslationBlocks']=array();
					foreach ($work['TranslationBlocks']as $id=>$block) {
						if (isset($data['block'.$id])) {
							$data['TranslationBlocks'][]= array(
								'translation' => $data['block'.$id],
								'from_segment' => $id
								);
							unset($data['block'.$id]);
						}
					}
					$model->update($data,$translationId);
				}
			}
			if (isset($post['submitquit']) || isset($post['cancel'])) {
				$this->_helper->redirector->gotoSimple('read',null,null,array('id'=>$translationId));
			}
		} else {
			foreach ($work['TranslationBlocks'] as $id=>$block) {
				$work['block'.$id]=$block['translation'];
			}
			$form->setDefaults($work);
		}
		$this->view->form=$form;
		$this->view->translation=$work;
	}
	
	
	public function cutAction() 
	{
	
		$request=$this->getRequest();
		$translationId=$request->getParam('id');
		$model = $this->_getModel();
		if (!$translation=$model->fetchTranslationWork($translationId)) {
		    throw new Zend_Controller_Action_Exception(sprintf('Translation "%d" does not exist.', $translationId), 404);
		}
		$workModel = new Model_Work();
			
		$srcTextId=$translation['OriginalWorkId'];
		if (!$srcText=$workModel->fetchOriginalWork($srcTextId)) {
		    throw new Zend_Controller_Action_Exception(sprintf('Text Id "%d" does not exist.', $srcTextId), 404);
		}
		$segToCut=$request->getParam('after');
		if ($segToCut<0 || $segToCut>=array_keys($srcText['Sentences'])) {
		    throw new Zend_Controller_Action_Exception(sprintf('Can not cut here (%d', $segToCut), 404);
		}
		$model->cut($translationId,$segToCut);
		$this->_helper->redirector->gotoSimple('edit',null,null,array('id'=>$translationId));

	}
	
	public function mergeAction() 
	{
      
		$request=$this->getRequest();
		$translationId=$request->getParam('id');
      
		$model=$this->_getModel();
		if (!$translation=$model->fetchTranslationWork($translationId)) {
			throw new Zend_Controller_Action_Exception(sprintf('Translation "%d" does not exist.', $translationId), 404);
		}
		$workModel = new Model_Work();
		$srcTextId = $translation['OriginalWorkId'];
		if (!$srcText=$workModel->fetchOriginalWork($srcTextId)) {
		    throw new Zend_Controller_Action_Exception(sprintf('Text Id "%d" does not exist.', $srcTextId), 404);
		}
		$segToMerge=$request->getParam('after');
		if ($segToMerge<0 || $segToMerge>=array_keys($srcText['Sentences'])) {
		    throw new Zend_Controller_Action_Exception(sprintf('Can not merge here (%d)', $segToCut), 404);
		}
		$model->merge($translationId,$segToMerge);
		$this->_helper->redirector->gotoSimple('edit',null,null,array('id'=>$translationId));

	}
	
	public function readAction()
	{	
			
	}
	
	
	protected function _getBlockList($transBlocks) 
	{
		$blockList=array();
		foreach ($transBlocks as $id=>$block) {
		    $blockList[$id]=$this->_countRows($block['source']);
		}
		return $blockList;
	}
	
	protected function _countRows($text) 
	{
		$count=0;
		$lines=explode("\n",$text);
		foreach ($lines as $line) {
		    $count+=floor(strlen($line) / 50)+1;
		}
		return $count;
	}
	
	
	public function concordAction()
	{
	
	
	}
	
}