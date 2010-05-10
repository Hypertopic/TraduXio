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
        if(!$work=$model->fetchTranslationWork($translationId,false)){
            throw new Zend_Controller_Action_Exception(sprintf(__("Translation %1\$s does not exist.", $translationId)), 404);
        }
        $form = new Form_TranslationEdit($this->_getBlockList($work['TranslationBlocks']));

        // In the translation-edit page every user can see/remove only his tags
        $username=Tdxio_Auth::getUserName();
        Tdxio_Log::alert($work['Tags'],"tags da visualizzare in edit");
        if(!empty($work['Tags'])){
            $tags=array();
            foreach($work['Tags'] as $key=> $tag){
                if(!($tag['user']== $username)){
                    unset($work['Tags'][$key]);
                }else{$tags[$tag['genre_name']][]=$tag;}
            }
        }
        $work['Tags']=$tags;
                   
        if ($request->isPost()) {
            Tdxio_Log::info('REQUEST IS POST');
            $post=$request->getPost();
            if (!isset($post['cancel'])) {
                if ($form->isValid($post)) {
                    $data=$form->getValues();
                    Tdxio_Log::info($data,'transedit 1');
                    $data['TranslationBlocks']=array();
                    foreach ($work['TranslationBlocks']as $id=>$block) {
                        if (isset($data['block'.$id])) {
                            $data['TranslationBlocks'][]= array(
                                'translation' => $data['block'.$id],
                                'from_segment' => $block['from_segment']
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
            throw new Zend_Controller_Action_Exception(sprintf(__("Translation %1\$s does not exist.", $translationId)), 404);
        }
        $workModel = new Model_Work();
            
        $srcTextId=$translation['OriginalWorkId'];
        if (!$srcText=$workModel->fetchOriginalWork($srcTextId)) {
            throw new Zend_Controller_Action_Exception(sprintf(__("Text Id %1\$s does not exist.", $srcTextId)), 404);
        }
        $segToCut=$request->getParam('after');
        if ($segToCut<0 || $segToCut>=array_keys($srcText['Sentences'])) {
            throw new Zend_Controller_Action_Exception(sprintf(__("Can not cut here (%1\$s)", $segToCut)), 404);
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
            throw new Zend_Controller_Action_Exception(sprintf(__("Translation %1\$s does not exist.", $translationId)), 404);
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
        $request=$this->getRequest();
        $translationId=$request->getParam('id');
        $translation = $this->_getModel()->fetchTranslationWork($translationId);
        $showBlocks=$request->getParam('sb');
        if(is_null($showBlocks)) $showBlocks = 1;
        
        $tagForm = new Form_Tag();
        if ($this->getRequest()->isPost()) {
        
            if ($tagForm->isValid($this->getRequest()->getPost())) {
                
                $data = $tagForm->getValues();
                Tdxio_Log::info($data,'dati di tag');
                return $this->_helper->redirector->gotoSimple('tag','tag',null,array('id'=>$translationId,'genre'=>$data['tag_genre'],'tag'=>$data['tag_comment']));
            }
        }
                
        $this->view->otherTranslations=$translation['OriginalWork']['Interpretations'];
        $workModel = new Model_Work();
        $this->view->hasTranslations=$workModel->hasTranslations($translationId);        
        $this->view->canTag = $workModel->isAllowed('tag',$translationId);
        $this->view->canManage = $workModel->isAllowed('manage',$translationId);
        $this->view->tagForm = $tagForm;
        $this->view->translation = $translation;
        $this->view->showBlocks = $showBlocks;
        $this->view->switchBlocks = ($showBlocks==1)?0:1;
        $this->view->blockOption = ($showBlocks==1)?__('HIDE BLOCKS'):__('SHOW BLOCKS');
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
        Tdxio_Log::info($this->getRequest()->getParams(),'params');
        $this->view->assign($this->getRequest()->getParams());
    }
    
        
    public function searchAction() {
        static $params=array('query');
        static $params_optional=array('transId','from','orig_filter','src_filter','dest_filter','returnStyle');
        
        $request=$this->getRequest();
        if ($request->isPost()) {
            $values=$request->getPost();
            Tdxio_Log::info($values,'search values');
            if ($this->_checkParams($params,$values,$params_optional)) {
                Tdxio_Log::info($request->getRawBody(),'post');
                $workModel=new Model_Work();
                $taggableModel=new Model_Taggable();
                $genreModel=new Model_Genre();
                foreach ($values as $name=>$value) {
                    $$name=$value;
                    $this->view->$name=$value;
                }
                //$src_filter="release:2008/book:10";
                //extract($values);
                $filters=array();
                $viewFilters=array();
                //$metadatas=array();
                $model=$this->_getModel();
                foreach (array('src','dest') as $type) {
                    //$metadatas[$type]=array_merge($genreModel->getGenres(),array('author'=>'author','language'=>'language'));
                    $var=$type."_filter";
                    Tdxio_Log::info($$var,$var);
                    if ($$var) {
                        $temp_filter=explode('/',$$var);
                        $filters[$type]=array();
                        foreach ($temp_filter as $v) {
                            list($field,$value) = explode(":",$v);
                            if ($field && $value) {
                                $filters[$type][$field]=$value;
                                $viewFilters[$var][]=$v;
                            }
                        }
                    }
                    Tdxio_Log::info($$var,$var);
                    //$this->view->$var=$$var;
                }
                //Tdxio_Log::info($metadatas,"metadatas");
                //$this->view->metadatas=$metadatas;
                $blocks=$model->search($query,$transId,$from,$filters);
                if ($blocks) {
                    $criterii_translation1=array();
                    $criterii_translation2=array();
                    $ids=array();
                    $fixedMetadatas=array();
                    foreach ($blocks as &$block) {
                        foreach (array('src','dest')  as $type) {
                            if (!isset($fixedMetadatas[$type])) $fixedMetadatas[$type]=array();
                            if (!isset($ids[$type])) $ids[$type]=array();
                            if (!in_array($block[$type.'_id'],$ids[$type])) {
                                $ids[$type][]=$block[$type.'_id'];
                            }
                            foreach (array('author','language') as $fixedMeta) {
                                if (!isset($fixedMetadatas[$type][$fixedMeta])) $fixedMetadatas[$type][$fixedMeta]=array();
                                $fixedMetadatas[$type][$fixedMeta][$block[$type.'_'.$fixedMeta]]=__($block[$type.'_'.$fixedMeta]);
                            }
                            $block[$type.'_language']=__($block[$type.'_language']);
                        }
                    }
                    $criterii=array();
                    $this->view->metadata=array();
                    foreach (array('src','dest')  as $type) {
                        Tdxio_Log::info($ids[$type],"ids $type");
                        $works=$workModel->fetchAllOriginalWorks($ids[$type]);
                        $tags = $taggableModel->getTags($ids[$type]);
                        if(isset($tags['Genres'])){unset($tags['Genres']);}//temporaneo in attesa di decidere se lasciare tag id o no
                        $this->view->metadata[$type]=array_merge($this->_getMetadatasFromTags($tags),$fixedMetadatas[$type]);
                        Tdxio_Log::info($this->view->metadata[$type],"criterii $type");
                    }
                }
                //$this->view->texts=$texts;
                $search=array('query'=>$query,'from'=>$from);
                if ($viewFilters) $search['filters']=$viewFilters;
                $this->view->filters=$filters;
                $this->view->currentSearch=$search;
                Tdxio_Log::info(count($texts));
                $tq=$model->getQuery($query);
                $this->view->transQuery=$tq;
                Tdxio_Log::info($query,'query, '.count($blocks).' results');
                $this->view->blocks=$blocks;
                $this->view->values=$values;
            }
            $viewScript='search';
            if (isset($returnStyle)) {
                $viewScript.='-'.$returnStyle;
            }
            $this->_helper->viewRenderer($viewScript);
        } else {
            throw new Zend_Controller_Action_Exception('Incorrect query.', 500);
        }
        //$this->view->render('search-json.phtml');
    }

    protected function _checkParams($params,$values,$optionals=array()) {
        $keys=array_keys($values);
        foreach ($params as $param) {
            if (in_array($param,$keys)) {
                unset($values[$param]);
            } else {
                throw new Zend_Controller_Action_Exception('Incorrect query, missing value '.$param, 500);
            }
        }
        foreach ($optionals as $optional) {
            if (in_array($optional,$keys)) {
                unset($values[$optional]);
            }
        }
        if (count($values)) {
            $incorrect=implode(',',array_keys($values));
            throw new Zend_Controller_Action_Exception('Incorrect query ('.$incorrect.')', 500);
        }
        return true;
    }
    
    public function getRule($request){
        $action = $request->action;
        $resource_id = $request->getParam('id');
        
        
        if(!is_null($resource_id)){ 
            if(!($this->_getModel()->entryExists(array('work_id'=>$resource_id))))
            {throw new Zend_Exception(sprintf('Translation Id "%d" does not exist.',$resource_id), 404);}
            $workModel = new Model_Work();
            $visibility=$workModel->getAttribute($resource_id,'visibility');
        }
        
        switch($action){
            case 'edit':
                if($request->isPost()){
                    $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);        
                }else{
                    $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility, 'notAllowed'=>true);        
                } break; 
            case 'cut':
                $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);    
                break;
            case 'merge': 
                $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);        
                break;              
            case 'read': 
                $rule = array('privilege'=> 'read','work_id' => $resource_id,'visibility'=>$visibility,'edit_privilege'=> 'edit');  
                break;  
            case 'concord': break;
            default:$rule = 'noAction';
        }               
        return $rule;
        
    }
    
    protected function _getMetadatasFromTags($tags_texts) {
        Tdxio_log::info($tags_texts,"tags_texts");
        $metadatas=array();
        foreach ($tags_texts as $text) {
            $this->_extractMetadata($text,$metadatas);
        }
        return $metadatas;
    }

    protected function _extractMetadata($text,&$metadata=array()) {
        foreach ($text as $tag) {
            $metadata[$tag['genre_name']][$tag['comment']]=$tag['comment'];
        }
    }

    
}
