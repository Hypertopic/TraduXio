<?php

/**
 * Work model
 *
 * Represents a single work entry.
 * 
 * @uses       Model_Taggable
 * @package    Traduxio
 * @subpackage Model
 */
class Model_Work extends Model_Taggable
{
    protected $_tableClass = 'Work';
    protected $_cutter;
    
    /**
     * Save a new entry
     *
     * @param  array $data
     * @return int|string
     */
     
    public function save(array $data)
    {
        // insert data into the table "work"
        $table  = $this->_getTable();
        $new_id = $table->insert($data);
        
        // insert data into the table "sentence"
        $sentences = $this->_getCutter()->getSentences($data['the_text']);
        unset($data['the_text']);

        $sentenceModel = new Model_Sentence();
        $sentenceModel->bulkSave($new_id,$sentences,true);
        return $new_id;
    }
    
    public function update(array $data, $id)
    {
        if (isset($data['insert_text']))  {
            $sentences = $this->_getCutter()->getSentences($data['insert_text']);
            
            $sentenceModel=new Model_Sentence();
            
            $offset=($sentenceModel->getLastSentenceNumber($id))+1;            
            $sentences_offset=array();
            foreach ($sentences as $i=>$seg) {
                $sentences_offset[$i+$offset]=$seg;
            }
            $sentenceModel->bulkSave($id,$sentences_offset,false);
            $segnums=array_keys($sentences_offset);
            $fromseg=$segnums[0];
            $toseg=end($segnums);
            Tdxio_Log::info($sentences_offset,'sentences aggiornate nella chiamata diretta');
            Tdxio_Log::info('fromseg is '.$fromseg.', toseg is '.$toseg);
            
            $table=$this->_getTable();
            Tdxio_Log::info($data);
            $new_id=$table->update($data,$table->getAdapter()->quoteInto('id = ?',$id));
            
            //aggiungi un translation block ad ogni traduzione di $id
            $this->addInterpretations($id,$fromseg,$toseg,$data['insert_text']);            
        }
        if (isset($data['visibility'])){
            $table=$this->_getTable();
            $new_id=$table->update($data,$table->getAdapter()->quoteInto('id = ?',$id));
            Tdxio_Log::info($new_id,'nuovo id');
        }
        return $new_id; 
    }
        
    public function createTranslation(array $data,$original_work_id)
    {
        // insert data into the table "work"
        $table  = $this->_getTable();
        $work_id = $table->insert($data);
        
        // insert data into the table "interpretation"
        $translationModel = new Model_Translation();
        $translationModel->create($work_id, $original_work_id);
        return $work_id;
    }

    protected function _getCutter() {
        if (null === $this->_cutter) {
            $this->_cutter = new Tdxio_Cutter();
        }
        return $this->_cutter;
    }

     /**
     * Fetch a work or a set of works from table work
     * given a single id or a set of work ids.
     * If the argument $id is a scalar, that work is fetched without filtering the result.
     * If the argument $id is an array, only the works that the user can read
     * and whose ids are contained in $id are fetched.
     * 
     * @param  int|array $id
     * @return int|string
     */
     
    public function fetchWork($id,$filter=true){
        $ids = $id;
        if(!is_array($id)){
            $ids = array($id);
        } 
        $table=$this->_getTable();
        $works = array();
        if($filter==true){
            $select = $this->getSelectCondAllowedWork('read'); 
            $works = $table->fetchAll($table->select()->where('id IN (?)',$ids)->where('id IN (?)',$select))->toArray();
        }else{ 
            $works = $table->fetchAll($table->select()->where('id IN (?)',$ids))->toArray(); 
        }
        if(!is_array($id)){
            return $works[0];
        }else {
            return $works;
        }
  
    }
    
    public function fetchOriginalWork($work_id){
    
        if(!$work = $this->fetchWork($work_id)){return null;}
        $sentenceModel= new Model_Sentence();
        $work['Sentences'] = $sentenceModel->fetchSentences($work_id);
        $content = '';
        foreach($work['Sentences'] as $key => $sentence){
            $content.=$sentence['content'];
        }
        $work['the_text']=$content;
        $translationModel = new Model_Translation();
        $work['Interpretations'] = $translationModel->fetchSentencesInterpretations($work_id);  
        $tags = ($this->getTags($work_id));
        Tdxio_Log::info($tags,"WCtags before normalization");
        if(!empty($tags)){
            $work['Tags'] = $this->normalizeTags($tags[$work_id]);
        }
        return $work;
    }

    public function fetchAllOriginalWorks($idList=null)
    {
        $table = $this->_getTable();
        $select1 = $this->getSelectCondOriginalWork('sentence');
        $select2 = $this->getSelectCondAllowedWork('read'); 
        if(is_null($idList)){           
            $select = $table->select()->from($table, array('id','title','author','language'))->where('id IN (?)',$select1)->where('id IN (?)',$select2);
        }else {//select only some specific original works
            $select = $table->select()->from($table, array('id','title','author','language'))->where('id IN (?)',$select1)->where('id IN (?)',$select2)->where('id IN (?)',$idList);
        }
        Tdxio_Log::info('stringa sql'.$select->__toString());
        return $table->fetchAll($select);
        
    }
    
    public function getSelectCondOriginalWork($table_name){
        $table = $this->_getTable();
        $db = $table->getAdapter();
        $select = $db->select()->distinct()->from($table_name,'work_id');
        
        return $select;     
    }
    
    public function getSelectCondAllowedWork($privilege)
    {
        $table = $this->_getTable();
        $db = $table->getAdapter();
        $user_name = Tdxio_Auth::getUserName();
        if(is_null($user_name)) $user_name = 'guest';
        $visibility = 'public';
    
        $select = $db->select()->from('work','id')->where('creator = ?',$user_name)
                                                  ->orWhere('visibility = ?',$visibility)
                                                  ->orWhere('id IN (?)',$db->select()->from('privileges','work_id')->where('user_id = ?',$user_name)->where('privilege = ?',$privilege));       
        return $select;     
        
    }
    
    public function fetchMyTranslationWorks($user){
        $table = $this->_getTable();
        $select1 = $this->getSelectCondOriginalWork('interpretation');
        $select2 = $table->select()->where('id IN (?)',$select1)->where('creator = ?',$user);
        $translations = $table->fetchAll($select2)->toArray();
        $translations = $this->addOriginalWorksToTranslationWorks($translations);
        return $translations;
    }
    
    public function addOriginalWorksToTranslationWorks($translations){
        
        if(!empty($translations)){
            $idList=array();
            foreach($translations as $key=>$trWork){
                $idList[$key]=$trWork['id'];            
            }
            $table = $this->_getTable();
            $db = $table->getAdapter();
            $select = $db->select()->from('interpretation',array('work_id','original_work_id'))->where('work_id IN (?)', $idList);
            $result = $db->fetchAll($select);
            $originalIds=array();
            foreach($result as $key=>$ids){
                $originalIds[$ids['work_id']]=$ids['original_work_id'];
            }       
            Tdxio_Log::info($originalIds,'originalIds');
            $result=$this->fetchWork($originalIds);
            $originalWorks=array();
            foreach($result as $key=>$origWork){
                $originalWorks[$origWork['id']]=$origWork;
            }
            
            foreach($translations as $key=>$trWork){
                $translations[$key]['original_work']=$originalWorks[$originalIds[$trWork['id']]];
                $translations[$key]['srcLang']=$translations[$key]['original_work']['language'];
            }                   
        }
        return $translations;
    }
    
    
    
    public function isOriginalWork($id)
    {
        $table = $this->_getTable();
        $db = $table->getAdapter();
        
        $select = $db->select()->from('sentence')->where('work_id = ?', $id);
        $result = $db->fetchAll($select);
        if(!empty($result))
            return true;
        else 
            return false;       
    
    }
    
    
    protected function addInterpretations($work_id,$fromseg,$toseg,$srcText){
        
        $table  = $this->_getTable();
        $intTable = new Model_DbTable_Interpretation();
        
        $select = $intTable->select()->distinct()->from($intTable,'work_id')->where('original_work_id = ?',$work_id); 
        $trnsltIds = $intTable->fetchAll($select);
        $trnsltIds = $trnsltIds->toArray();
        $trModel = new Model_Translation();
            
        Tdxio_Log::info($trnsltIds,'trnsltIds');
        
        foreach($trnsltIds as $key=>$transl){
            Tdxio_Log::info($transl,'Guarda');              
            $my_id = $transl['work_id'];
            $data['TranslationBlocks']=array();
            $data['TranslationBlocks'][]=array('original_work_id' => $work_id,
                                            'work_id'=> $my_id,
                                            'from_segment'=>$fromseg,
                                            'to_segment'=>$toseg,
                                            'translation'=>"");
            Tdxio_Log::info($data,'data');
            $trModel->update($data,$my_id);
            Tdxio_Log::info('Adding translation block in text with id '.$my_id);
                            
        }
    }

    public function getAttribute($work_id, $attrname){

        $work=$this->fetchWork($work_id,false);
      
        if(isset($work[$attrname])){
            return $work[$attrname];
        }

        throw new Zend_Exception('The specified attribute does not exist in the database.');
    }

    public function isAllowed($privilege_name,$work_id){
    
        $user_name = Tdxio_Auth::getUserName();
        $user_role = Tdxio_Auth::getUserRole();
        $privilege = array( 'user_id' => $user_name,
                            'role' => $user_role,
                            'work_id' => $work_id,
                            'privilege' => $privilege_name,
                            'visibility' => $this->getAttribute($work_id, 'visibility')
                    );
        $privilegeModel = new Model_Privilege();
        return $privilegeModel->exist($privilege); 
                
    }
    
    public function getWorkPrivileges($id){ 
        $visibility=$this->getAttribute($id,'visibility');
        $privilegeModel= new Model_Privilege();
        $privilegeTable=$privilegeModel->_getTable();
        
        if($visibility=='custom'){
        
        $select = $privilegeTable->select()->where('work_id = ?',$id)
                                                  ->orWhere('work_id is NULL AND visibility = ?',$visibility);
        
        Tdxio_Log::info('stringa sql in getWorkPrivileges'.$select->__toString());
          
        $list = $privilegeTable->fetchAll($select);
    
        }else{
            $list=$privilegeModel->fetchByFields(array('visibility = ?', $visibility));
            $list = $list->toArray();
        }
        
        Tdxio_Log::info($list);
        $newlist=array();
        foreach($list as $key=>$item){
            $item['user_id']=($item['user_id']=='')?'All users are ' : 'User '.$item['user_id'].' is ';
            $item['privilege']=($item['privilege']=='')?' do everything for ':$item['privilege'];
            $newlist[$key]['plaintext']= $item['user_id'].' allowed to '.$item['privilege'].' this text.';
            $newlist[$key]['id']=$item['id'];
            $newlist[$key]['work_id']=$item['work_id'];
        }       
        return $newlist;
    }
    
    public function removePrivilege($id_list=array(),$attribute_value=array()){
        $privilegeModel= new Model_Privilege();
        $privilegeModel->delete($id_list,$attribute_value);     
    }
    
    public function addPrivilege($data){
        $privilegeModel=new Model_Privilege();
        $userModel= new Model_User();
        $data['privilege']=$privilegeModel->_dbPrivilegeList[$data['privilege']];
        $data['user_id']=($data['user']=='all')?null:$data['user'];
        unset($data['user']);
        
        $data['role']=null;
        Tdxio_Log::info($data['user_id']);
        Tdxio_Log::info($data,'data da inserire');
        $id=$privilegeModel->save($data);
        Tdxio_Log::info($id);
    }
    

}
