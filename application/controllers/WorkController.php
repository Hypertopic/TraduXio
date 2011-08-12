<?php

/**
 * Work controller
 *
 * 
 * @uses       Tdxio_Controller_Abstract
 * @package    Traduxio
 * @subpackage Controller
 */ 
 
class WorkController extends Tdxio_Controller_Abstract
{
    protected $_modelname='Work'; 
    public $MONTHSEC = 1296000; //15 giorni
    public $CREATETIME = 900; //15 minuti
    public $MAX_NEWS = 30; //maximum number of last news to see in last news section
    public $MAX_TIME = 1000; //maximum age in days of the news you want to visualize
        
    public function init()
    {
        // Local to this controller only; affects all actions,
        // as loaded in init: 
       
    }

        /**
         * The index, or landing, action will be concerned with listing the entries 
         * that already exist.
         *
         * Assuming the default route and default router, this action is dispatched 
         * via the following urls:
         * - /work/
         * - /work/index
         *
         * @return void
         */
    public function indexAction()
    {
        Tdxio_Log::info('flusso: 1 CONTROLLER INDEX');
        $work = $this->_getModel();
        Tdxio_Log::info('not here');
        $entries = $work->fetchAllOriginalWorks();
        //Tdxio_Log::info($entries);
        $sort=array();
        
        if(!is_null($entries)){
            foreach ($entries as $entry) {
                         
                if (isset($entry['language'])) {
                    $lang=__($entry['language']);
                    if (!isset($sort[$lang])) {
                        $sort[$lang]=array();
                    }
                    if (!isset($entry['author']) || $entry['author']==='' ) {
                        $entry['author']=__('Anonymous');
                    }
                    $author=__($entry['author']);
                    if(!isset($sort[$lang][$author])) {
                        $sort[$lang][$author]=array();
                    }
                    $sort[$lang][$author][]=$entry;
                }
            }
            foreach ($sort as $l=>$entry) {
				ksort($sort[$l],SORT_STRING);
			}
        }
        $langModel = new Model_Language();        
        $browserLang = $langModel->getBrowserLang(3);
        $news = $this->getNews($browserLang['id']);
        Tdxio_Log::info($browserLang['id'],'bbbbrows');
        Tdxio_Log::info($news,'newentries');
        if(!empty($sort)){ksort($sort,SORT_LOCALE_STRING);}     
        $this->view->entries=$sort;        
        //$this->view->home = true; //non serve più da quando è stato eliminato dal file layout.phtml
        $this->view->news = $news;
        $this->view->newsLang = $browserLang['id'];
    }
        
        
    public function depositAction()
    {       
        $form = new Form_TextDeposit();

        if ($this->getRequest()->isPost()) {
            
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $data = $form->getValues();
                $data['creator']=Tdxio_Auth::getUserName();
                $model = $this->_getModel();
                $new_id = $model->save($data);
                $histModel = new Model_History();  
                $histModel->addHistory($new_id,5);   
                Tdxio_Log::info($data);
                return $this->_helper->redirector->gotoSimple('newread','work',null,array('id'=>$new_id));
            }
        }
        $this->view->form = $form;
    } 
    
    public function readAction(){
    
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $model = $this->_getModel();
        $tagForm = new Form_Tag();
        
        if (!$id || !($work=$model->fetchOriginalWork($id))) {
            
            throw new Zend_Controller_Action_Exception(sprintf(__("Work %1\$d does not exist or you don't have the rights to see it ", $id)), 404);
        }   
        
        if(empty($work['Sentences'])){
            return $this->_helper->redirector->gotoSimple('read','translation',null,array('id'=>$id));
        }
        Tdxio_Log::info($work,'work read');

		$this->view->canTag = $model->isAllowed('tag',$id);
        $taglist = new Zend_View();
        $taglist->setScriptPath(APPLICATION_PATH.'/views/scripts/tag/');        
        $taglist->assign('tags',$work['Tags']);
        $taglist->assign('genres',$work['Genres']);
        $taglist->assign('workid',$work['id']);
        $taglist->assign('userid',$this->view->userid);
        $taglist->assign('canTag',$this->view->canTag);
        $this->view->tagbody=$taglist->render('taglist.phtml');
        
        $this->view->hasTranslations=$model->hasTranslations($id);     
        $this->view->canManage = $model->isAllowed('manage',$id);
        $this->view->work = $work;
        Tdxio_Log::info($work,'work/read work');
        $this->view->tagForm = $tagForm;
    }
    
     public function printAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $model = $this->_getModel();
        
        if($model->isTranslationWork($id)){
			$trModel = new Model_Translation();
			$origWork = $trModel->fetchTranslationOriginalWork($id);
			$origId = $origWork['id'];
			return $this->_helper->redirector->gotoUrl('/translation/print/id/'.$id); 
		}
        $work = $model->fetchWork($id);
        
        if (!$id || !($work=$model->fetchOriginalWork($id))) {
            
            throw new Zend_Controller_Action_Exception(sprintf(__("Work %1\$d does not exist or you don't have the rights to see it ", $id)), 404);
        }   
        $this->view->work = $work;
    }
    
    public function newreadAction(){
		
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $model = $this->_getModel();
        $tagForm = new Form_Tag();
        $work = $model->fetchWork($id);
        
        if($model->isTranslationWork($id)){
			$trModel = new Model_Translation();
			$origWork = $trModel->fetchTranslationOriginalWork($id);
			$origId = $origWork['id'];
			return $this->_helper->redirector->gotoUrl('/work/newread/id/'.$origId.'#tr'.$id); 
		}
        $work=$model->fetchOriginalWork($id);
        Tdxio_Log::info($work,'origwork');        
        if((!$id) ||  (!$work)){
            Tdxio_Log::info('get in here');        
            throw new Zend_Controller_Action_Exception(sprintf(__("Work %1\$d does not exist or you don't have the rights to see it ", $id)), 404);
        }
        $this->view->canTag = $model->isAllowed('tag',$id);
        $taglist = new Zend_View();
		$taglist->setScriptPath(APPLICATION_PATH.'/views/scripts/tag/');        
		$taglist->assign('tags',$work['Tags']);
		$taglist->assign('genres',$work['Genres']);
		$taglist->assign('workid',$work['id']);
		$taglist->assign('userid',$this->view->userid);
		$taglist->assign('canTag',$this->view->canTag);
		if($model->isAllowed('tag',$id)){
			$tagForm = new Form_Tag(); 
			$taglist->assign('form',$tagForm);
		}       
		$this->view->tagbody=$taglist->render('taglist.phtml');
        
        $this->view->hasTranslations=$model->hasTranslations($id);   
        Tdxio_Log::info($this->view->hasTranslations,'hastrans');     
        $this->view->canManage = $model->isAllowed('manage',$id);
        $this->view->canTranslate = $model->isAllowed('translate',$id);
        $this->view->canDelete = $model->isAllowed('delete',$id);
        $this->view->canEdit = $model->isAllowed('edit',$id);
        $this->view->work = $work;
        $session = new Zend_Session_Namespace('MenuIcons');
        if(isset($session->state)){$this->view->iconsState=$session->state;}
        Tdxio_Log::info('till here');
    }
    

    
	public function ajaxreadAction(){
		
		$request = $this->getRequest();
		$id = $request->getParam('id');
		Tdxio_Log::info($request,'ababa');
		$qtity = $request->getParam('qtity');//numero di segmenti/sentences da scaricare
		$translations = array();

		$trId=$request->getParam('trId');
		$trWork = null;
		Tdxio_Log::info($trId,'steptwo');
		$userid = Tdxio_Auth::getUserName();

		$model = $this->_getModel();
		if (!$id || !($work=$model->fetchOriginalWork($id))) {
			$this->view->response = false;
            $this->view->message = array('code'=>1,'text'=> __("Work %1\$d does not exist.",$id));
		}else{
			if(empty($work['Interpretations'])){$trId=null;}
            else{
				$trId = (array_key_exists($trId,$work['Interpretations']))?$trId:key($work['Interpretations']);
				$trModel = new Model_Translation();
				$trWork = $trModel->fetchTranslationWork($trId);                
				foreach($work['Interpretations'] as $id=>$tr){$translations[] = $tr;}
				
				$this->view->canTag = $model->isAllowed('tag',$trId);
				$taglist = new Zend_View();
				$taglist->setScriptPath(APPLICATION_PATH.'/views/scripts/tag/');        
				$taglist->assign('tags',$trWork['Tags']);
				$taglist->assign('genres',$trWork['Genres']);
				$taglist->assign('workid',$trWork['id']);
				$taglist->assign('userid',$this->view->userid);
				$taglist->assign('canTag',$this->view->canTag);
				   
				$this->view->tagbody=$taglist->render('taglist.phtml');
				$canDel = ($userid == $trWork['creator'])?true:$model->isAllowed('delete',$trId);
				$this->view->trPrivileges = array('manage'=>$model->isAllowed('manage',$trId),'edit'=>$model->isAllowed('edit',$trId),'del'=>$canDel);
            }
            $work['Interpretations'] = $translations;
		}
        Tdxio_Log::info($work,'ajaxread0');
		$this->view->response = true;
		$this->view->message  = array('code'=>0,'text'=> __("OK"));   
        $this->view->work = $work;
        $this->view->trWork = $trWork;
        $this->view->trId = $trId;
        
        Tdxio_Log::info($trWork,'ajaxread1');
    }
        
    public function createtrAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id');
        $model = $this->_getModel();     
        if (!$id || !$origWork=$model->fetchOriginalWork($id)) {
            throw new Zend_Controller_Action_Exception(sprintf(__("Work %1\$d does not exist.", $id)), 404);
        }
        $form = new Form_AjaxWorkTranslate();
        if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {				
				$test = $form->isValid($request->getPost());
				Tdxio_Log::info($test,'testtesttest');
				$data=$request->getPost();
				Tdxio_Log::info($data,'ajax new translation data');
				$userid = Tdxio_Auth::getUserName();                
				$data['creator']=$userid;
				unset($data['id']);
				$newId=$model->createTranslation($data,$id);
				if(!is_null($newId)){
					$histModel = new Model_History();        
					$histModel->addHistory($newId,5);   
					$this->view->response = true;
					$this->view->newId = $newId;
					$this->view->values = $data; 
					$this->view->message = array('code'=>0,'text'=> __("OK"));
				}else{
					$this->view->response = false;
					$this->view->newId = null;   
					$this->view->values = null;  
					$this->view->message = array('code' => 1,'text'=>__("DB not modified"));
				}
			}else{
				$this->view->response = false;
				$this->view->newId = null;    
				$this->view->values = null;  
				$this->view->message = array('code' => 3,'text'=>__("Invalid form. Please fill all the required (*) fields."));
			}
		}
	}
    
    public function translateAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $model = $this->_getModel();     
        if (!$id || !$origWork=$model->fetchOriginalWork($id)) {
            throw new Zend_Controller_Action_Exception(sprintf(__("Work %1\$d does not exist.", $id)), 404);
        }
        $form = new Form_Translate();
        
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data=$form->getValues();
                Tdxio_Log::info($data,'new translation data');
                $userid = Tdxio_Auth::getUserName();                
                $data['creator']=$userid;
                $newId=$model->createTranslation($data,$id);
                $histModel = new Model_History();        
                $histModel->addHistory($newId,5);   
                return $this->_helper->redirector->gotoSimple('edit','translation',null,array('id'=>$newId));
            }
        }
        $this->view->form=$form;
        $this->view->origWork=$origWork;
    }
        
    public function myAction(){
        $user = Tdxio_Auth::getUserName(); 
        if(!is_null($user)){
            $work = $this->_getModel();
            $myTranslations = $work->fetchMyTranslationWorks($user);
            $srcLangs=array();
            foreach($myTranslations as $trWork){
                $srcLangs[$trWork['srcLang']][$trWork['language']][]=$trWork;
            }
            $this->view->myEntries = $srcLangs;
            
            Tdxio_Log::info($srcLangs,'my translations');       
        }
    }
    
    public function getvalueAction(){
		$model=$this->_getModel();         
        $request = $this->getRequest();
        $value=$request->getParam('value');
        $id=$request->getParam('id');
        if (!$id || !($work=$model->fetchWork($id))) {
			$this->view->data = array('response' => false, 'value' => null,'message' => array('code' => 1,'text'=>__("Invalid work id")));
		}
        $this->view->data = array('response' => true,'message' => array('code' => 0,'text'=>__("OK")),'value'=>$work[$value]);
        $this->_helper->viewRenderer('jsonresponse');
    }
    
    public function getformAction(){
        $request = $this->getRequest();
        $type=$request->getParam('type');
        $formname = 'Form_AjaxWork'.ucfirst($type);
        if($type=='translate')
			$form = new $formname($request->getParam('id'));			
	/*	elseif($type=='sentencetag')
			$form = new $formname($request->getParam('id'),$request->getParam('number'));	*/
		else
			$form = new $formname;
        Tdxio_Log::info($form,'quaqua');
        $this->view->form = $form;
        Tdxio_Log::info(json_encode($form),'tent');
        $this->view->response = true;
        $this->view->message = array('code' => 0,'text'=>__("OK"));
    }
    
        
    public function ajaxextendAction(){
        Tdxio_Log::info('got here');
        $request = $this->getRequest();
        $id=$request->getParam('id');
        $model=$this->_getModel(); 
        
        if (!$id || !($work=$model->fetchWork($id))) {
            throw new Zend_Controller_Action_Exception(sprintf('Work Id "%d" does not exist.', $id), 404);
        }   
        
        if(!$model->isOriginalWork($id)) {
            throw new Zend_Controller_Action_Exception(sprintf('Cannot extend a translation. Edit it instead.'), 404);
        }
        
        if($id && $work=$model->fetchOriginalWork($id))
        {  
            if ($this->getRequest()->isPost())
            {
                $values=$request->getPost();
                if($values['extendtext']!=null){
					$data = array('insert_text'=>$values['extendtext']);
					Tdxio_Log::info($data,'valori form');                

					$result=$model->update($data,$id);
					if($result>0){
						$histModel = new Model_History();
						$histModel->addHistory($id,1);  
						$this->view->response=true;
						$this->view->addedText = $values['extendtext'];
						$this->view->message = array('code'=>0,'text'=> __("OK"));

					}else{
						$this->view->response = false;
						$this->view->addedText = '';    
						$this->view->message = array('code' => 1,'text'=>__("DB not modified"));
					}
				}else{
					$this->view->response = false;
					$this->view->addedText = '';    
					$this->view->message = array('code' => 3,'text'=>__("Invalid form. Please fill all the required (*) fields."));
				}
            }
        }
    }
    
    public function extendAction(){
        $request = $this->getRequest();
        $id=$request->getParam('id');
        $model=$this->_getModel(); 
        
        if (!$id || !($work=$model->fetchWork($id))) {
            throw new Zend_Controller_Action_Exception(sprintf(__("Work %1\$d does not exist.", $id)), 404);
        }   
        
        if(!$model->isOriginalWork($id)) {
            throw new Zend_Controller_Action_Exception(sprintf(__("Cannot extend a translation. Edit it instead.")), 404);
        }
        
        $sentenceModel = new Model_Sentence();
        
        if($id && $work=$model->fetchOriginalWork($id))
        {           
            $form = new Form_TextExtend(); 
            $lastsentence = $sentenceModel->fetchSentence($id,$sentenceModel->getLastSentenceNumber($id));
            Tdxio_Log::info($lastsentence);
            $lasttext = ' '.$lastsentence[0]['content'];
            
            if ($this->getRequest()->isPost()) 
            {
                if ($form->isValid($request->getPost())){
                    $model = $this->_getModel();
                    $data=$form->getValues();
                    Tdxio_Log::info($data,'old extend form values');
                    unset($data['submit']);
                    
                    $result=$model->update($data,$id);
                    if($result>0){
                        $histModel = new Model_History();
                        $histModel->addHistory($id,1);  
                    }
                    return $this->_helper->redirector->gotoSimple('read',null,null, array('id'=>$id));                        
                }
            }
            $this->view->form=$form;
            $this->view->text=$work;
            $this->view->lasttext=$lasttext;
            
        }else {
            throw new Zend_Exception(__("Couldn't find work %1\$d",$id));
        }
    }   
    
    public function editAction(){
        
        $request = $this->getRequest();
        $id=$request->getParam('id');
        $model=$this->_getModel();
        if ($id && ($work=$model->fetchWork($id))) {
            $form = new Form_WorkEdit($id);
            
            if ($request->isPost()) {
                $post=$request->getPost();
                if(!isset($post['cancel'])){
                
                    if ($form->isValid($post)) {
                        
                        $data=$form->getValues();
                        Tdxio_Log::info($data,'dati form work edit');
                        $result=$model->update($data,$id); 
                        if($result>0){
							$histModel = new Model_History();        
							$histModel->addHistory($id,0);               
						}
                    }
                }
                return $this->_helper->redirector->gotoSimple('read',null,null, array('id'=>$id));
            }
            $this->view->form=$form;
            $this->view->work=$work;
        } else {
            throw new Zend_Exception("Couldn't find work $id");
        }
        
    }
    
    public function metaeditAction(){
		$request = $this->getRequest();
        $id=$request->getParam('id');
        $model=$this->_getModel();
		$data = array($request->getParam('elName')=>$request->getParam('value'));
/*		$author = $request->getParam('author');
		$title = $request->getParam('title');
		$translator = $request->getParam('translator');
	
		if(!is_null($author)){$data['author']=$author;$newText = $author;}
		if(!is_null($title))
			$data['title']=$title;
		*/
		Tdxio_Log::info($data,'datata');
		$result=$model->update($data,$id); 
		if($result>0){
			$histModel = new Model_History();        
			$histModel->addHistory($id,0);  
			$this->view->response=true;
			$this->view->newText = $request->getParam('value');
			$this->view->message = array('code' => 0, 'text' => __("OK"));

		}else{
			$this->view->response = false;
			$this->view->newText = null;    
			$this->view->message = array('code' => 1,'text'=>__("DB not modified"));
		}
	}

    public function manageAction(){
                
        $request = $this->getRequest();
        $id= $request->getParam('id');
        $model=$this->_getModel();
        
        $visibility=$model->getAttribute($id,'visibility');
        if($visibility=='custom'){
            $addform = new Form_AddPrivilege();            
            $privilegeList=$model->getWorkPrivileges($id);          
            $this->view->addform=$addform;              
            if(!is_null($privilegeList)){
                $remform = new Form_RemovePrivilege($id,$privilegeList);
                $this->view->remform=$remform;
                if($this->getRequest()->isPost()) {
                    $post = $request->getPost();
                    Tdxio_Log::info($post,'ispost rem');
                    if($remform->isValid($post)) {
                        Tdxio_Log::info($post,'isvalid rem');
                        $data=$remform->getValues();
                        if($post['submit']==__("Remove Privilege")){
                            Tdxio_Log::info($data);
                            $remove_list=array_keys($data,1);
                            Tdxio_Log::info($remove_list,'remove list');
                            if(!empty($remove_list)){                               
                                $model->removePrivilege($remove_list,array());                              
                                return $this->_helper->redirector->gotoSimple('manage',null,null, array('id'=>$id));
                            }
                        }
                        Tdxio_Log::info($data,'dati privilege remove');
                    }
                    $this->view->remform=$remform;
                }
            }
            if($this->getRequest()->isPost()) {
                $post = $request->getPost();
                Tdxio_Log::info($post,'ispost add');
                if($addform->isValid($post)) {
                    Tdxio_Log::info($post,'isvalid add');
                    $data=$addform->getValues();
                    Tdxio_Log::info($data,'dati privilege add 1');
                    if($post['submit']==__("Add Privilege")){
                        Tdxio_Log::info($data,'dati privilege add 2');                    
                        unset($data['submit']);
                        $data['work_id']=$id;
                        $data['visibility']='custom';
                        if(!is_null($model->addPrivilege($data)))
                            return $this->_helper->redirector->gotoSimple('manage',null,null, array('id'=>$id));
                        else $addform->setDescription(__("The privilege was not added because it already exists"));
                    }
                }
            }
            $this->view->link=__('Switch to Standard Privileges');
        }else{
            $stdform = new Form_StdPrivilege();
            $this->view->stdform=$stdform;
            if($this->getRequest()->isPost()) {
                $post = $request->getPost();
                Tdxio_Log::info($post,'ispost std');
                if($stdform->isValid($post)) {
                    Tdxio_Log::info($post,'isvalid std');
                    $data=$stdform->getValues();
                    Tdxio_Log::info($data,"dati privilege std");
                    if($post['submit']==__("Save")){    
                        Tdxio_Log::info($data,"manage data");                    
                        unset($data['submit']);
                        $model->update($data,$id);
                        return $this->_helper->redirector->gotoSimple('newread',null,null, array('id'=>$id));
                    }               
                }
            }
            $this->view->stdform->setDefaults(array('visibility'=>$visibility));
            $this->view->link=__('Switch to Custom Privileges');
        }           
        $this->view->visibility=$visibility;
        $this->view->work_id=$id;       
    }
    
    public function switchAction(){
        $model=$this->_getModel();
        $request = $this->getRequest();
        $id=$request->getParam('id');
        $visibility=$model->getAttribute($id,'visibility');
    
        if($visibility=='public' or $visibility=='private'){
            // change field visibility in table text to custom
            // go to manage page
            $data=array('visibility'=>'custom');
            $model->update($data,$id);
        }elseif($visibility=='custom'){
            //delete all custom privileges for the text  $id
            // change field visibility in table text to private
            // go to manage page
            $attr_value=array('work_id'=>$id);
            $model->removePrivilege(array(),$attr_value); 
            $data=array('visibility'=>'private');
            $model->update($data,$id);
        }
        return $this->_helper->redirector->gotoSimple('manage',null,null, array('id'=>$id));
    }
    
    public function deleteAction(){
        $request = $this->getRequest();
        $id= $request->getParam('id');
        $model=$this->_getModel();
        if(!($model->hasTranslations($id))){$orig_id=$model->delete($id);}
        
        if( is_null($orig_id) ){ $this->_redirect($_SERVER['HTTP_REFERER']); }
        elseif( $orig_id<0 ){ return $this->_helper->redirector('index'); }
        else{ return $this->_helper->redirector->gotoSimple('newread',null,null, array('id'=>$orig_id)); }        
    }
    
    public function ajaxdeleteAction(){
        $request = $this->getRequest();
        $id= $request->getParam('id');
        $model=$this->_getModel();
        if(!($model->hasTranslations($id))){$orig_id=$model->delete($id);}
        
        if( is_null($id) ){ 	
			$this->view->response = false;
			$this->view->newId = $id;   
			$this->view->values = null;  
			$this->view->message = array('code' => 1,'text'=>__("Invalid work id.No works deleted."));
		}
        elseif( $id<0 ){ 
			$this->view->response = true;
			$this->view->newId = $id;   
			$this->view->values = null;  
			$this->view->message = array('code' => 0,'text'=>__("Original Work deleted")); }
        else{ 
			$this->view->response = true;
			$this->view->newId = $id;   
			$this->view->values = null;  
			$this->view->message = array('code' => 0,'text'=>__("Translation deleted")); }    
			$this->_helper->viewRenderer('createtr');    
    }

    public function historyAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $model=$this->_getModel();
        $histModel = new Model_History();
        $this->view->work = $model->fetchWork($id);
        $this->view->history = $histModel->getHistory($id);
    }

    protected function tagSentence()
    {
        
    } 
        
    public function codeList($code,array $params){
        $codeList = array(
            0=>__("%1\$s, text modified by %2\$s",$params['title'],$params['user']),
            1=>__("%1\$s, text extended by %2\$s",$params['title'],$params['user']),
            2=>__("%1\$s, translation of %2\$s, modified by %3\$s",$params['title'],$params['origtitle'],$params['user']),    
            3=>__("New tag [%2\$s:%1\$s] for the text %3\$s added by %4\$s",$params['tag'],$params['genre'],$params['taggedText'],$params['user']),
            4=>__("Removed tag [%2\$s:%1\$s] from the text %3\$s by %4\$s",$params['tag'],$params['genre'],$params['taggedText'],$params['user']),
            5=>__("%1\$s, new text added by %2\$s",$params['title'],$params['user']),
            6=>__("%1\$s, new translation of %2\$s added by %3\$s",$params['title'],$params['origtitle'],$params['user'])         
        );
        return $codeList[$code];
    }
    
    public function addInfo($row){
        //$row has fields: [user][date][work_id][message]{[params]}[id][title][author][language][created][creator][visibility][modified]
        $model = $this->_getModel();
        $row['date']=strtotime($row['date']);
        $infoRow['age']=time() - $row['date'];
          
        $row['title']=($row['title']=='')?'<i>No Title</i>':$row['title'];
            
        if($row['message']==3 or $row['message']==4){
            $tag = '<a class="news_link" href="'.$this->view->makeUrl('/work/newread/id/'.$row['work_id']).'">'.$row['params']['tag'].'</a>';
            $taggedText = '<a href="'.$this->view->makeUrl('/work/newread/id/'.$row['work_id']).'">"'.$row['title'].'"</a>';
            $infoRow['phrase'] =  $this->codeList($row['message'],array('tag'=>$tag,'genre'=>$row['params']['genre'],'taggedText'=>$taggedText,'user'=>$row['user']));
        }elseif($row['message']==0 or $row['message']==1){
            $title = '<a class="news_link" href="'.$this->view->makeUrl('/work/newread/id/'.$row['work_id']).'">"'.$row['title'].'"</a>';
            $infoRow['phrase'] =$this->codeList($row['message'],array('title'=>$title,'user'=>$row['user']));
        }elseif($model->isTranslationWork($row['work_id'])){
            $title = '<a class="news_link" href="'.$this->view->makeUrl('/work/newread/id/'.$row['work_id']).'">"'.$row['title'].'"</a>';
            $trModel = new Model_Translation();
            $origWork = $trModel->fetchTranslationOriginalWork($row['work_id']);
            $tempTitle=($origWork['title']=='')?'<i>No Title</i>':$origWork['title'];
            $origtitle = '<a href="'.$this->view->makeUrl('/work/newread/id/'.$origWork['id']).'">"'.$tempTitle.'"</a>';
            $newCode = ($row['message']==2)?2:6;
            $infoRow['phrase'] = $this->codeList($newCode,array('title'=>$title,'origtitle'=>$origtitle,'user'=>$row['user'])); 
        }elseif($row['message']==5){
            $title = '<a class="news_link" href="'.$this->view->makeUrl('/work/newread/id/'.$row['id']).'">"'.$row['title'].'"</a>';
            $infoRow['phrase'] = $this->codeList($row['message'],array('title'=>$title,'user'=>$row['creator']));
        }else{$infoRow['phrase'] = 'il controllo perde alcuni casi';}
        
        return $infoRow;
    }
    
    public function getNews($lang){
        $model = $this->_getModel();
        $newTexts = $model->getNewTranslatedWorks($lang,$this->MAX_TIME,$this->MAX_NEWS);
        return $newTexts;
    }
    
     /* no more used after news panel modification
    public function sortByAge($list){
        
        $sortedList=array();
        foreach($list as $key=>$item){
            $sortedList[$item['age']][]=$item;    
        }
        
        if(!empty($sortedList)){ksort($sortedList);}
        return($sortedList);
    }
    
   
    public function equivalent(array $a, array $b, array $paramList){
        foreach($paramList as $param){
            if($param=='date'){
                $adate = getdate(strtotime($a['date']));
                $bdate = getdate(strtotime($b['date']));
                Tdxio_Log::info($adate,'date1');
                Tdxio_Log::info($bdate,'date2');
                $diff = array_diff(array($adate['year'],$adate['yday']),array($bdate['year'],$bdate['yday']));
                if(!empty($diff))
                    return false;
            }elseif($a[$param]!=$b[$param]){
                return false;
            }
        }
        return true;
    }*/
    
    public function canAction(){
		$model = $this->_getModel();
		$request = $this->getRequest();
		$privilege = $request->getParam('privilege');
		$id = $request->getParam('id');
		$user = Tdxio_Auth::getUserName();
		$can = $model->isAllowed($privilege,$id);
		$this->view->data = array('response'=>$can, 'message'=>array('code'=>$can?0:3,'text'=>$can?__("OK"):__("Error")));
		$this->_helper->viewRenderer('jsonresponse');		
	}
    
    
  public function getuserAction(){
		$request=$this->getRequest(); 
		$id=$request->getParam('id'); 
		$user = Tdxio_Auth::getUserName();
		Tdxio_Log::info($user,'userr');
		$this->view->data = array('response'=>true,'message'=>array('code'=>0,'text'=>__("OK")),'user'=>$user);
	}
    
    
    public function transliterateAction(){
		$request = $this->getRequest();
		$text = $request->getParam('text');
		$srcLang = $request->getParam('srcLang');
		$destLang = $request->getParam('destLang');
		Tdxio_Log::info($request,'tuttok');
		try{$transliterator = new Tdxio_Filter_Transliteration();}catch(Zend_Exception $e){Tdxio_Log::info('non crea il filtro');}
		Tdxio_Log::info('dopo creazione filter');
		if($srcLang!=$destLang)
			$tlText = $transliterator->filter(array('text'=>$text,'srcLang'=>$srcLang,'destLang'=>$destLang));
		else
			$tlText = $text;
		$this->view->data = array('response'=>true, 'message'=>array('code'=>0,'text'=>__("OK")),'transliteratedText'=>$tlText);
		$this->_helper->viewRenderer('jsonresponse');		
	}
    
    public function getRule($request){
        $action = $request->action;
        $resource_id = $request->getParam('id');
        $visibility = null;
        $rule = 'noAction';
        Tdxio_Log::info($request,'request');
        Tdxio_Log::info($resource_id,'resource_id');
        
        if(!is_null($resource_id)){ 
            if(!($this->_getModel()->entryExists(array('id'=>$resource_id))))
            {throw new Zend_Exception(sprintf(__("Work %1\$d does not exist.",$resource_id)), 404);}
            $visibility=$this->_getModel()->getAttribute($resource_id,'visibility');
            Tdxio_Log::info($visibility,'visibilita');
        }
        
        switch($action){
            case 'index': 
                //$rule = array('privilege'=> 'read','work_id' => -1);      
                break; 
            case 'deposit': 
                if($request->isPost()){
                    $rule = array('privilege'=> 'create','work_id' => -1 );       
                }else{$rule = array('privilege'=> 'create','work_id' => -1, 'notAllowed'=>true);} 
                break;
			//case 'getuser': $rule = array('privilege'=> 'translate','work_id' => $resource_id);break;
			case 'createtr':
				$rule = array('privilege'=> 'translate','work_id' => $resource_id);
            break;
            case 'translate':
                if($request->isPost()){
                    $rule = array('privilege'=> 'translate','work_id' => $resource_id);
                }else{
                    $rule = array('privilege'=> 'translate','work_id' => $resource_id, 'notAllowed'=>true);
                }break;
            
            case 'history':$rule = array('privilege'=> 'read','work_id' => $resource_id,'visibility'=>$visibility);   
                break;
            case 'can':$rule = array('privilege'=> $request->getParam('privilege'),'work_id' => $resource_id,'visibility'=>$visibility);   
            break;  
            case 'ajaxread':  
				$trId = $request->getParam('trId');
				if($trId!=null && $trId!='' && $this->_getModel()->entryExists(array('id'=>$trId))){					
                    $resource_id = $trId;
                    $visibility=$this->_getModel()->getAttribute($trId,'visibility');
				}
                $rule = array('privilege'=> 'read','work_id' => $resource_id,'visibility'=>$visibility,'edit_privilege'=> 'edit','translate_privilege'=> 'translate');      
                break;
            case 'newread':
            case 'read':
                if($request->isPost()){
                    $rule = array('privilege'=> 'tag','work_id' => $resource_id);
                }else{
                        $rule = array('privilege'=> 'read','work_id' => $resource_id,'visibility'=>$visibility,'edit_privilege'=> 'edit','translate_privilege'=> 'translate');      
                }break; 
            case 'getform': $rule = array('privilege'=> 'tag','work_id' => $resource_id);break;            
            case 'getvalue':
            case 'metaedit': $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);break; 
            case 'edit':
                if($request->isPost()){
                    $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);        
                }else{$rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility,'notAllowed'=>true);} 
                break;
            case 'my': $rule = array('privilege'=> 'translate','work_id'=>-1); //work_id = -1 is to ensure it does not count privileges with work_id !=null
                break;
            case 'extend': 
            case 'ajaxextend':
                if($request->isPost()){
                    $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);        
                }else{
                    $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility, 'notAllowed'=>true);    
                } break;
            case 'switch':  
            case 'manage':
                if($request->isPost()){
                        $rule = array('privilege'=> 'manage','work_id' => $resource_id,'visibility'=>$visibility);      
                    }else{
                        $rule = array('privilege'=> 'manage','work_id' => $resource_id, 'visibility'=>$visibility, 'notAllowed'=>true); 
                    } break;  
            case 'ajaxdelete':
            case 'delete':
                $rule = array('privilege'=> 'delete','work_id' => $resource_id,'visibility'=>$visibility);      
                break;
            default:$rule = 'noAction';
        }               
        return $rule;
        
    }
    
    
}
