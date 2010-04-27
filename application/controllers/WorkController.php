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
    public $_privilegeList=array();
    public $MONTHSEC = 1296000; //15 giorni
    
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->_privilegeList = array( __('Read Text PRV'), __('Edit Text PRV'), __('Create Translation PRV'), __('Manage PRV'), __('Tag Text PRV'));
        return parent::__construct($request, $response, $invokeArgs);
    }
    
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
        $entries = $work->fetchAllOriginalWorks();
        //Tdxio_Log::info($entries);
                
        $newModWorks = array();
        $sort=array();
        
        if(!is_null($entries)){
            foreach ($entries as $entry) {
                
                if(!is_null($NMentry=$this->newModified($entry,'orig'))){$newModWorks[]=$NMentry;}
                
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
        }
        
        $news = $this->sortByAge(array_merge($this->getNews(),$newModWorks));
        Tdxio_Log::info($entries,'newentries');
        if(!empty($sort)){ksort($sort,SORT_STRING);}     
        $this->view->entries=$sort;        
        $this->view->home = true;
        $this->view->news = $news;
    }
        
        
    public function depositAction()
    {       
        $form = new Form_TextDeposit();

        if ($this->getRequest()->isPost()) {
            
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $data = $form->getValues();
                $data['creator']=Tdxio_Auth::getUserName();
                $model = $this->_getModel();
                $model->save($data);
                Tdxio_Log::info($data);
                return $this->_helper->redirector('index');
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
            
            throw new Zend_Controller_Action_Exception(sprintf(__("Work Id %1\$s does not exist or you don't have the rights to see it ", $id)), 404);
        }   
        
        if(empty($work['Sentences'])){
            return $this->_helper->redirector->gotoSimple('read','translation',null,array('id'=>$id));
        }
        Tdxio_Log::info($work,'work read');
        if ($this->getRequest()->isPost()) {            
            if ($tagForm->isValid($this->getRequest()->getPost())) {                
                $data = $tagForm->getValues();                     
                return $this->_helper->redirector->gotoSimple('tag','tag',null,array('id'=>$id,'genre'=>$data['tag_genre'],'tag'=>$data['tag_comment']));  
            }
        }
        $this->view->canTag = $model->isAllowed('tag',$id);
        $this->view->canManage = $model->isAllowed('manage',$id);
        $this->view->work = $work;
        $this->view->tagForm = $tagForm;
    }
    
    public function translateAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $model = $this->_getModel();     
        if (!$id || !$origWork=$model->fetchOriginalWork($id)) {
            throw new Zend_Controller_Action_Exception(sprintf('Work Id "%d" does not exist.', $id), 404);
        }
        $form = new Form_Translate();
        
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data=$form->getValues();
                $userid = Tdxio_Auth::getUserName();                
                $data['creator']=$userid;
                $newId=$model->createTranslation($data,$id);
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
        }else return $this->_helper->redirector->gotoSimple('index','work');
    }
    
    public function extendAction(){
        $request = $this->getRequest();
        $id=$request->getParam('id');
        
        $model=$this->_getModel(); 
        
        if (!$id || !($work=$model->fetchWork($id))) {
            throw new Zend_Controller_Action_Exception(sprintf('Work Id "%d" does not exist.', $id), 404);
        }   
        
        if(!$model->isOriginalWork($id)) {
            throw new Zend_Controller_Action_Exception(sprintf('Cannot extend a translation. Edit it instead.'), 404);
        }
        
        $sentenceModel = new Model_Sentence();
        
        if($id && $work=$model->fetchOriginalWork($id))
        {           
            $form = new Form_TextExtend(); 
            $lastsentence = $sentenceModel->fetchSentence($id,$sentenceModel->getLastSentenceNumber($id));
            Tdxio_Log::info($lastsentence,'pipipopo');
            $lasttext = ' '.$lastsentence[0]['content'];
            
            if ($this->getRequest()->isPost()) 
            {
                if ($form->isValid($request->getPost())){
                    $model = $this->_getModel();
                    $data=$form->getValues();
                                    
                    unset($data['submit']);
                    
                    $newId=$model->update($data,$id);
                    return $this->_helper->redirector->gotoSimple('read',null,null, array('id'=>$id));
                }
            }
            $this->view->form=$form;
            $this->view->text=$work;
            $this->view->lasttext=$lasttext;
            
        }else {
            throw new Zend_Exception("Couldn't find text $id");
        }
    }   
    
    public function editAction(){
        /*
         *  $request = $this->getRequest();
        $id=$request->getParam('id');
        $model=$this->_getModel();
        if ($id && ($text=$model->fetchEntry($id))) {
            if ($text['translation_of']) {
                $this->log('init form translation');
                $form = $this->_getForm('edit','translation');
            } else {
                $form = $this->_getForm('edit');
            }
            if ($this->getRequest()->isPost()) {
                if ($form->isValid($request->getPost())) {

                    $model = $this->_getModel();
                    $data=$form->getValues();
                    $this->log($data);
                    $newId=$model->update($data,$id);

                    return $this->_helper->redirector->gotoSimple('read',null,null, array('id'=>$id));
                }
            }
            $form->setDefaults($text);
            $this->view->form=$form;
            $this->view->text=$text;
        } else {
            throw new Zend_Exception("Couldn't find text $id");
        }
         * 
         * */
        
    }

    public function manageAction(){
                
        $request = $this->getRequest();
        $id= $request->getParam('id');
        $model=$this->_getModel();
        $visibility=$model->getAttribute($id,'visibility');
        if($visibility=='custom'){
            $addform = new Form_AddPrivilege($this->_privilegeList);            
            $privilegeList=$model->getWorkPrivileges($id);          
            $this->view->addform=$addform;              
            if(!is_null($privilegeList)){
                $remform = new Form_RemovePrivilege($id,$privilegeList);
                $this->view->remform=$remform;
                if($this->getRequest()->isPost()) {
                    Tdxio_Log::info('ispost rem');
                    if($remform->isValid($request->getPost())) {
                        Tdxio_Log::info('isvalid rem');
                        $data=$remform->getValues();
                        if($data['submit']==__("Remove Privilege")){
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
                Tdxio_Log::info('ispost add');
                if($addform->isValid($request->getPost())) {
                    Tdxio_Log::info('isvalid add');
                    $data=$addform->getValues();
                    Tdxio_Log::info($data,'dati privilege add 1');
                    if($data['submit']==__("Add Privilege")){
                        Tdxio_Log::info($data,'dati privilege add 2');
                        unset($data['submit']);
                        $data['work_id']=$id;
                        $data['visibility']='custom';
                        $model->addPrivilege($data);
                        return $this->_helper->redirector->gotoSimple('manage',null,null, array('id'=>$id));
                    }
                }
            }
            $this->view->link=__('Switch to Standard Privileges');
        }else{
            $stdform = new Form_StdPrivilege();
            $this->view->stdform=$stdform;
            if($this->getRequest()->isPost()) {
                Tdxio_Log::info('ispost std');
                if($stdform->isValid($request->getPost())) {
                    Tdxio_Log::info('isvalid std');
                    $data=$stdform->getValues();
                    if($data['submit']=="Save"){                        
                        unset($data['submit']);
                        Tdxio_Log::info($data,"manage data");
                        $model->update($data,$id);
                        return $this->_helper->redirector->gotoSimple('read',null,null, array('id'=>$id));
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

    protected function tagSentence()
    {
        
    } 
    
    public function newModified($item,$type){
        
        if(isset($item['created'])){
            if(($item['title']=='') OR (empty($item['title'])))
            {$item['title']='<i>'.__('No Title').'</i>';}
            
            if(($item['orig_title']=='') OR (empty($item['orig_title'])))
            {$item['orig_title']='<i>'.__('No Title').'</i>';}
            
            $NMitem=$item;
            if(((!isset($item['modified']))or($item['modified']-$item['created']<10))and(time() - strtotime($item['created']) < $this->MONTHSEC))
            {                
                $NMitem['age']=time() - strtotime($item['created']);
            
                if($type=='orig'){
                    $title = '<a class="news_link" href="'.$this->view->makeUrl('/work/read/id/'.$item['id']).'">'.$item['title'].'</a>';
                    $NMitem['phrase'] = __("\"%1\$s\", new text added by %2\$s",$title,$item['creator']);
                }
                elseif($type=='tra'){
                    $title = '<a class="news_link" href="'.$this->view->makeUrl('/translation/read/id/'.$item['id']).'">'.$item['title'].'</a>';
                    $origtitle = '<a href="'.$this->view->makeUrl('/work/read/id/'.$item['original_work_id']).'">'.$item['orig_title'].'</a>';
                    $NMitem['phrase'] = __("\"%1\$s\", new translation of %2\$s added by %3\$s",$title,$origtitle,$item['creator']);
                }
                elseif($type=='tag'){
                    $tag = '<a class="news_link" href="'.$this->view->makeUrl('/work/read/id/'.$item['taggable']).'">'.$item['comment'].'</a>';
                    $taggedText = '<a href="'.$this->view->makeUrl('/work/read/id/'.$item['taggable']).'">'.$item['title'].'</a>';
                    $NMitem['phrase'] =  __("\"%1\$s\", new (%2\$s) tag for %3\$s added by %4\$s",$tag,$item['genre_name'],$taggedText,$item['user']);
                }
            }elseif(($item['created'] < $item['modified']) and (time() - strtotime($item['modified']) < $this->MONTHSEC))
            {                
                $NMitem['age']=time() - strtotime($item['modified']);
            
                if($type=='orig'){
                    $title = '<a class="news_link" href="'.$this->view->makeUrl('/work/read/id/'.$item['id']).'">'.$item['title'].'</a>';
                    //$user = 'TEMPUSER';
                    //$NMitem['phrase'] =   __("\"%1\$s\", text modified by %2\$s",$title,$user);
                    $NMitem['phrase'] =   __("\"%1\$s\", the text has been modified",$title);
                }
                elseif($type=='tra'){
                    $title = '<a class="news_link" href="'.$this->view->makeUrl('/translation/read/id/'.$item['id']).'">'.$item['title'].'</a>';
                    $origtitle = '<a href="'.$this->view->makeUrl('/work/read/id/'.$item['original_work_id']).'">'.$item['orig_title'].'</a>';
                    //$user = 'TEMPUSER';
                    //$NMitem['phrase'] =   __("\"%1\$s\", translation of %2\$s, modified by %3\$s",$title,$origtitle,$user);
                    $NMitem['phrase'] =   __("\"%1\$s\", translation of %2\$s, has been modified",$title,$origtitle);
                }
                elseif($type=='tag'){
                    $tag = '<a class="news_link" href="'.$this->view->makeUrl('/work/read/id/'.$item['taggable']).'">'.$item['comment'].'</a>';
                    $taggedText = '<a href="'.$this->view->makeUrl('/work/read/id/'.$item['taggable']).'">'.$item['title'].'</a>';
                    $NMitem['phrase'] =   __("\"%1\$s\" (%2\$s) tag for %3\$s has been modified by %4\$s",$tag,$item['genre_name'],$taggedText,$item['user']);
                }
            }else{return null;}            
        }    
        return $NMitem;
    }
    
    public function getNews(){
        // get visible texts inserted or modified in the last 30 days
        $model = $this->_getModel();
        $transl = $model->getNewModTransl();
        $news = array();
        
        foreach($transl as $key=> $item){
            if(!is_null($NMitem=$this->newModified($item,'tra'))){$news[]=$NMitem;}
        }
        
        // get tags inserted on own texts in the last 30 days
        $taggModel = new Model_Taggable();
        if(is_null($user = Tdxio_Auth::getUserName())) $user = 'guest';
        $tags = $taggModel->getNewModTags($user);   
               
        foreach($tags as $key=> $item){
            if(!is_null($NMitem=$this->newModified($item,'tag'))){$news[]=$NMitem;}
        }   
        return $news;
    }
    
    public function sortByAge($list){
        
        $sortedList=array();
        foreach($list as $key=>$item){
            $sortedList[$item['age']][]=$item;    
        }
        
        if(!empty($sortedList)){ksort($sortedList);}
        return($sortedList);
    }
    
    public function getRule($request){
        $action = $request->action;
        $resource_id = $request->getParam('id');
        
        $rule = 'noAction';
        Tdxio_Log::info($request,'request');
        Tdxio_Log::info($resource_id,'resource_id');
        
        if(!is_null($resource_id)){ 
            if(!($this->_getModel()->entryExists(array('id'=>$resource_id))))
            {throw new Zend_Exception(sprintf('Work Id "%d" does not exist.',$resource_id), 404);}
            $visibility=$this->_getModel()->getAttribute($resource_id,'visibility');
            Tdxio_Log::info($visibility,'visibilita');
        }
        
        switch($action){
            case 'index': 
                $rule = array('privilege'=> 'read','work_id' => null);      
                break; 
            case 'deposit': 
                if($request->isPost()){
                    $rule = array('privilege'=> 'create','work_id' => null );       
                }else{$rule = array('privilege'=> 'create','work_id' => null, 'notAllowed'=>true);} 
                break; 
            case 'translate':
                if($request->isPost()){
                    $rule = array('privilege'=> 'translate','work_id' => $resource_id);
                }else{
                    $rule = array('privilege'=> 'read','work_id' => $resource_id, 'notAllowed'=>true);
                }break;
            case 'read':
                if($request->isPost()){
                    $rule = array('privilege'=> 'tag','work_id' => $resource_id);
                }else{
                        $rule = array('privilege'=> 'read','work_id' => $resource_id,'visibility'=>$visibility,'edit_privilege'=> 'edit');      
                }break; 
            case 'edit':
                /*      if($request->isPost()){
                        $rule = array('privilege'=> 'edit','text_id' => $resource_id,'visibility'=>$visibility);        
                    }else{$rule = array('privilege'=> 'edit','text_id' => $resource_id,'visibility'=>$visibility,'notAllowed'=>true);       
                    } break; */
            case 'my': break;                   
            case 'extend':
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
            default:$rule = 'noAction';
        }               
        return $rule;
        
    }
    
    
}
