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
    
    public function ajaxeditAction() {
		$request=$this->getRequest(); 
		$model= $this->_getModel();
        $translationId=$request->getParam('id'); 
        if(!$work=$model->fetchTranslationWork($translationId,false)){
            throw new Zend_Controller_Action_Exception(sprintf(__("Translation %1\$s does not exist.", $translationId)), 404);
        }       
        
        if ($request->isPost()) {
            $data=$request->getPost();
            
            Tdxio_Log::info($data,'trajaxedit');  
            $data['TranslationBlocks']=array();                   
			foreach ($work['TranslationBlocks']as $id=>$block) {
				if (isset($data['block'.$id])) {
					$translation = $data['block'.$id];
					Tdxio_Log::info('block'.$id.' is set');
					$data['TranslationBlocks'][]= array(
						'translation' => $data['block'.$id],
						'from_segment' => $block['from_segment']
						);
						Tdxio_Log::info($data);
					unset($data['block'.$id]);
				}
			}
			$model->update($data,$translationId);   
			$histModel = new Model_History();
			$histModel->addHistory($translationId,2);  
		}
		$result = 0;
		//$result = $model->merge($translationId,$segToMerge);
        if($result==0){
            Tdxio_Log::info($data,'form values in trajedit');
            $this->view->data = array('response'=>true,'message'=>array('code'=>0,'text'=>__("OK")),'newText'=>$translation);
        }else{
            $this->view->data = array('response'=>false,'message'=>array('code'=>1,'text'=>__("ERROR, couldn't save the translation")));
        } 
		$this->_helper->viewRenderer('refresh');
	}

    public function editAction() {
        $request=$this->getRequest();
        $translationId=$request->getParam('id');
        Tdxio_Log::info($request,'viaggio');
        $model= $this->_getModel();
        $workModel = new Model_Work();
        if(!$work=$model->fetchTranslationWork($translationId,false)){
            throw new Zend_Controller_Action_Exception(sprintf(__("Translation %1\$s does not exist.", $translationId)), 404);
        }
        $form = new Form_TranslationEdit($this->_getBlockList($work['TranslationBlocks']));
        $form->setAction($request->getRequestUri());
        // In the translation-edit page every user can see/remove only his tags
        $username=Tdxio_Auth::getUserName();
        Tdxio_Log::alert($work['Tags'],"tags da visualizzare in edit");
        $tags=array();
        if(!empty($work['Tags'])){
            foreach($work['Tags'] as $key=> $tag){
                if(!($tag['user']== $username)){
                    unset($work['Tags'][$key]);
                }else{$tags[$tag['genre']][]=$tag;}
            }
        }
        $work['Tags']=$tags;
        
        $taglist = new Zend_View();
        $taglist->setScriptPath(APPLICATION_PATH.'/views/scripts/tag/');        
        $taglist->assign('tags',$tags);
        $taglist->assign('genres',$work['Genres']);
        $taglist->assign('workid',$translationId);
        $taglist->assign('userid',$this->view->userid);
        $taglist->assign('canTag',$workModel->isAllowed('tag',$translationId));
        $this->view->tagbody=$taglist->render('taglist.phtml');
                   
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
                    $histModel = new Model_History();
                    $histModel->addHistory($translationId,2);                   
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
    
    public function saveAction()
    {
        $request=$this->getRequest();
        $post = $request->getPost();
        Tdxio_Log::info($post,'posted');      
        
        $value = $this->_getParam('value', '');
       
        $this->view->newText = $value;
    }
    
    
    public function cutAction() 
    {
    
        $request=$this->getRequest();
        $translationId=$request->getParam('id');
        $model = $this->_getModel();
        if (!$translation=$model->fetchTranslationWork($translationId)) {
            throw new Zend_Controller_Action_Exception(sprintf(__("Translation %1\$d does not exist.", $translationId)), 404);
        }
        $workModel = new Model_Work();
            
        $srcTextId=$translation['OriginalWorkId'];
        if (!$srcText=$workModel->fetchOriginalWork($srcTextId)) {
            throw new Zend_Controller_Action_Exception(sprintf(__("Text Id %1\$d does not exist.", $srcTextId)), 404);
        }
        $segToCut=$request->getParam('after');
        if ($segToCut<0 || $segToCut>=array_keys($srcText['Sentences'])) {
            throw new Zend_Controller_Action_Exception(sprintf(__("Can not cut here (%1\$d)", $segToCut)), 404);
        }
        $model->cut($translationId,$segToCut);
        $segToRedirect = $this->getFirstSegmentOf($segToCut,$translation['TranslationBlocks']);
        $this->_helper->redirector->gotoUrl('translation/edit/id/'.$translationId."/#segment-".$segToRedirect);

    }
    
    public function ajaxcutAction(){
        $request=$this->getRequest();
        $translationId=$request->getParam('id');
        $segToCut=$request->getParam('after');
        $model=$this->_getModel();
        $workModel = new Model_Work();
        
        if ((!$work=$workModel->fetchWork($translationId))||!($workModel->isTranslationWork($translationId))) {
            $this->view->data = array('response'=>false,'message'=>array('code'=>1,'text'=>__("Translation %1\$d does not exist.", $translationId)));
            throw new Zend_Controller_Action_Exception(sprintf(__("Translation %1\$d does not exist.", $translationId)), 404);
        }
             
        $blocks=$model->fetchInterpretations($translationId);
        $lastBlock = end($blocks);
        if ($segToCut<0 || $segToCut>=$lastBlock['to_segment']) {
            $this->view->data = array('response'=>false,'message'=>array('code'=>1,'text'=>__("Can not merge here %1\$d", $segToCut)));
            throw new Zend_Controller_Action_Exception(sprintf(__("Can not merge here %1\$d", $segToCut)), 404);
        }
        
        $result = $model->cut($translationId,$segToCut);
        if($result==0){
            $segToRedirect = $this->getFirstSegmentOf($segToCut,$blocks);
//        $this->_helper->redirector->gotoUrl('translation/edit/id/'.$translationId."/#segment-".$segToRedirect);
        
            $blocks = $model->fetchInterpretations($translationId);
            Tdxio_Log::info($blocks,'blocks after cut');
            $this->view->data = array('response'=>true,'message'=>array('code'=>0,'text'=>__("OK")),'newblocks'=>$blocks,'segToRed'=>$segToRedirect);
        }else{
            $this->view->data = array('response'=>false,'message'=>array('code'=>1,'text'=>__("ERROR, couldn't cut on position %1\$d ",$segToCut)));
        }
        $this->_helper->viewRenderer('refresh');
    }
    
    public function ajaxmergeAction(){
        $request=$this->getRequest();
        $translationId=$request->getParam('id');      
        $segToMerge=$request->getParam('after');
        $model=$this->_getModel();
        $workModel = new Model_Work();
        $model=$this->_getModel();
        
        if ((!$work=$workModel->fetchWork($translationId))||!($workModel->isTranslationWork($translationId))) {
            $this->view->data = array('response'=>false,'message'=>array('code'=>1,'text'=>__("Translation %1\$s does not exist.", $translationId)));
            throw new Zend_Controller_Action_Exception(sprintf(__("Translation %1\$d does not exist.", $translationId)), 404);
        }
                
        $blocks=$model->fetchInterpretations($translationId);
        $lastBlock = end($blocks);
        if ($segToMerge<0 || $segToMerge>=$lastBlock['to_segment']) {
            $this->view->data = array('response'=>false,'message'=>array('code'=>1,'text'=>__("Can not merge here %1\$d", $segToMerge)));
            throw new Zend_Controller_Action_Exception(sprintf(__("Can not merge here %1\$d", $segToMerge)), 404);
        }
        $result = $model->merge($translationId,$segToMerge);
        if($result==0){
            $segToRedirect = $this->getFirstSegmentOf($segToMerge,$blocks);             
            $blocks = $model->fetchInterpretations($translationId);
            Tdxio_Log::info($blocks,'blocks after merge');
            $this->view->data = array('response'=>true,'message'=>array('code'=>0,'text'=>__("OK")),'newblocks'=>$blocks,'segToRed'=>$segToRedirect);
        }else{
            $this->view->data = array('response'=>false,'message'=>array('code'=>1,'text'=>__("ERROR, couldn't merge on position %1\$d ",$segToMerge)));
        } 
        $this->_helper->viewRenderer('refresh');
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
            throw new Zend_Controller_Action_Exception(sprintf('Can not merge here (%d)', $segToMerge), 404);
        }
        $model->merge($translationId,$segToMerge);
        $segToRedirect = $this->getFirstSegmentOf($segToMerge,$translation['TranslationBlocks']);
        $this->_helper->redirector->gotoUrl('translation/edit/id/'.$translationId."/#segment-".$segToRedirect);

    }
    
    
    public function readAction()
    {           
        $request=$this->getRequest();
        $translationId=$request->getParam('id');
        $translation = $this->_getModel()->fetchTranslationWork($translationId);
        Tdxio_Log::info($translation,'translation workk');
        $showBlocks=$request->getParam('sb');
        if(is_null($showBlocks)) $showBlocks = 1;
        
        $tagForm = new Form_Tag();
        
        $taglist = new Zend_View();
        $taglist->setScriptPath(APPLICATION_PATH.'/views/scripts/tag/');        
        $taglist->assign('tags',$translation['Tags']);
        $taglist->assign('genres',$translation['Genres']);
        $taglist->assign('workid',$translationId);
        $taglist->assign('userid',$this->view->userid);
        $this->view->tagbody=$taglist->render('taglist.phtml');
        
        $this->view->otherTranslations = $translation['OriginalWork']['Interpretations'];
        $workModel = new Model_Work();
        $this->view->hasTranslations=$workModel->hasTranslations($translationId);        
        $this->view->canTag = $workModel->isAllowed('tag',$translationId);
        $this->view->canManage = $workModel->isAllowed('manage',$translationId);
        $this->view->tagForm = $tagForm;
        $this->view->translation = $translation;
        $this->view->showBlocks = $showBlocks;
        $this->view->switchBlocks = ($showBlocks==1)?0:1;
        $this->view->blockOption = ($showBlocks==1)?__('Hide blocks'):__('Show blocks');
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
                    Tdxio_Log::info($$name,$name);
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
                if(!isset($transId))$transId=null;
                if(!isset($from))$from=null;
				if(empty($filters))$filters=array();
                
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
                $search=array('query'=>$query,'from'=>$from);
                if ($viewFilters) $search['filters']=$viewFilters;
                $this->view->filters=$filters;
                $this->view->currentSearch=$search;
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
            case 'save':
            case 'ajaxedit':
            case 'edit':
                if($request->isPost()){
                    $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);        
                }else{
                    $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility, 'notAllowed'=>true);        
                } break; 
            case 'ajaxcut':
            case 'cut':
                $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);    
                break;
            case 'ajaxmerge':
            case 'merge': 
                $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);        
                break;              
            case 'read': 
                $rule = array('privilege'=> 'read','work_id' => $resource_id,'visibility'=>$visibility,'edit_privilege'=> 'edit');  
                break;  
             case 'concord': 
			 case 'search':             
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
    
    public function getFirstSegmentOf($seg,$blocks){
        foreach($blocks as $key=>$block){
            if($block['from_segment']<=$seg and $seg<=$block['to_segment'])
                return $block['from_segment'];
        }
        return null;        
    }

}
