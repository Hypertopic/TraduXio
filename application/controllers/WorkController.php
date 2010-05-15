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
    public $CREATETIME = 900; //15 minuti
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
        }
        $news = $this->getNews();
        Tdxio_Log::info($news,'newentries');
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
                $new_id = $model->save($data);
                $histModel = new Model_History();        
                $histModel->addHistory($new_id,5);   
                Tdxio_Log::info($data);
                return $this->_helper->redirector->gotoSimple('read','work',null,array('id'=>$new_id));
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
        
        $this->view->hasTranslations=$model->hasTranslations($id);        
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
            Tdxio_Log::info($lastsentence);
            $lasttext = ' '.$lastsentence[0]['content'];
            
            if ($this->getRequest()->isPost()) 
            {
                if ($form->isValid($request->getPost())){
                    $model = $this->_getModel();
                    $data=$form->getValues();
                                    
                    unset($data['submit']);
                    
                    $newId=$model->update($data,$id);
                    $histModel = new Model_History();
                    $histModel->addHistory($id,1);  
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
                        $newId=$model->update($data,$id); 
                        $histModel = new Model_History();        
                        $histModel->addHistory($id,0);               
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
    
    public function deleteAction(){
        $request = $this->getRequest();
        $id= $request->getParam('id');
        $model=$this->_getModel();
        if(!($model->hasTranslations($id))){$orig_id=$model->delete($id);}
        
        if( is_null($orig_id) ){ $this->_redirect($_SERVER['HTTP_REFERER']); }
        elseif( $orig_id<0 ){ return $this->_helper->redirector('index'); }
        else{ return $this->_helper->redirector->gotoSimple('read',null,null, array('id'=>$orig_id)); }        
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
            0=>__("%1\$s, text modified by %2\$s",$params[0],$params[1]),
            1=>__("%1\$s, text extended by %2\$s",$params[0],$params[1]),
            2=>__("%1\$s, translation of %2\$s modified by %3\$s",$params[0],$params[1],$params[2]),    
            3=>__("New tag [%2\$s:%1\$s] for the text \"%3\$s\" added by %4\$s",$params[0],$params[1],$params[2],$params[3]),
            4=>__("Removed tag [%2\$s:%1\$s] from the text \"%3\$s\" by %4\$s",$params[0],$params[1],$params[2],$params[3]),
            5=>__("%1\$s, new text added by %2\$s",$params[0],$params[1]),
            6=>__("%1\$s, new translation of %2\$s added by %3\$s",$params[0],$params[1],$params[2])         
        );
        return $codeList[$code];
    }
    
    public function addInfo($row){
        //$row has fields: [user][date][work_id][message]{[params]}[id][title][author][language][created][creator][visibility][modified]
        $model = $this->_getModel();
        $row['date']=strtotime($row['date']);
        $infoRow['age']=time() - $row['date'];
          
        if($row['message']==3 or $row['message']==4){
            $tag = '<a class="news_link" href="'.$this->view->makeUrl('/work/read/id/'.$row['work_id']).'">'.$row['params']['tag'].'</a>';
            $taggedText = '<a href="'.$this->view->makeUrl('/work/read/id/'.$row['work_id']).'">'.$row['title'].'</a>';
            $infoRow['phrase'] =  $this->codeList($row['message'],array($tag,$row['params']['genre'],$taggedText,$row['user']));
        }elseif($row['message']==0 or $row['message']==1){
            $title = '<a class="news_link" href="'.$this->view->makeUrl('/work/read/id/'.$row['work_id']).'">"'.$row['title'].'"</a>';
            $infoRow['phrase'] =$this->codeList($row['message'],array($title,$row['user']));
        }elseif($model->isTranslationWork($row['work_id'])){
            $title = '<a class="news_link" href="'.$this->view->makeUrl('/translation/read/id/'.$row['work_id']).'">"'.$row['title'].'"</a>';
            $trModel = new Model_Translation();
            $trlWork = $trModel->fetchTranslationWork($row['work_id']);
            $origtitle = '<a href="'.$this->view->makeUrl('/work/read/id/'.$trlWork['OriginalWorkId']).'">"'.$trlWork['OriginalWork']['title'].'"</a>';
            $newCode = ($row['message']==2)?2:6;
            $infoRow['phrase'] = $this->codeList($newCode,array($title,$origtitle,$row['user'])); 
        }elseif($row['message']==5){
            $title = '<a class="news_link" href="'.$this->view->makeUrl('/work/read/id/'.$row['id']).'">"'.$row['title'].'"</a>';
            $infoRow['phrase'] = $this->codeList($row['message'],array($title,$row['creator']));
        }else{$infoRow['phrase'] = 'il controllo perde alcuni casi';}
        
        return $infoRow;
    }
    public function getNews(){
        $model = $this->_getModel();
        $histModel = new Model_History();
        $lastHistory = $histModel->getAllRecentHistory(30);//get history of last 15 days
        if(empty($lastHistory))return null;
        $referenceRow = $lastHistory[0];
        Tdxio_Log::info($referenceRow,'riga di riferimento');
        $selectedHistory[0]=$this->addInfo($lastHistory[0]);
        foreach($lastHistory as $key=>$row){
            if(!($this->_equals($row,$referenceRow,array('user','date','work_id','message')))){
                $selectedHistory[]=$this->addInfo($row);
                $referenceRow = $row;
            }            
        }
        return $selectedHistory;
    }
    
    
    public function sortByAge($list){
        
        $sortedList=array();
        foreach($list as $key=>$item){
            $sortedList[$item['age']][]=$item;    
        }
        
        if(!empty($sortedList)){ksort($sortedList);}
        return($sortedList);
    }
    
    public function _equals(array $a, array $b, array $paramList){
        foreach($paramList as $param){
            if($param=='date'){
                if($a[$param]!=$b[$param])
                    return false;
            }elseif($a[$param]!=$b[$param]){
                return false;
            }
        }
        return true;
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
                //$rule = array('privilege'=> 'read','work_id' => null);      
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
            
            case 'history':
            case 'read':
                if($request->isPost()){
                    $rule = array('privilege'=> 'tag','work_id' => $resource_id);
                }else{
                        $rule = array('privilege'=> 'read','work_id' => $resource_id,'visibility'=>$visibility,'edit_privilege'=> 'edit');      
                }break; 
            case 'edit':
                if($request->isPost()){
                    $rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility);        
                }else{$rule = array('privilege'=> 'edit','work_id' => $resource_id,'visibility'=>$visibility,'notAllowed'=>true);} 
                break;
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
            case 'delete':
                $rule = array('privilege'=> 'delete','work_id' => $resource_id,'visibility'=>$visibility);      
                break;
            default:$rule = 'noAction';
        }               
        return $rule;
        
    }
    
    
}
