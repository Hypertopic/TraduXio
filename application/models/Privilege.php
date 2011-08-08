<?php 

/**
 * Privilege model
 *
 * Represents a Privilege
 * 
 * @uses       Model_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_Privilege extends Model_Abstract
{
    protected $_tableClass = 'Privilege';    
    
    public $_dbPrivilegeList=array();
    public $_includeReadList = array('read','edit','translate','manage','tag','delete');
    
    public function __construct($class_name=null,$idField=null,$contentField=null)
    {
        $this->_dbPrivilegeList=array('read'=>__('Read text'),'edit'=>__('Edit text'),'translate'=>__('Create translation'),'manage'=>__('Manage'),'tag'=>__('Tag text'));
        return parent::__construct($class_name=null,$idField=null,$contentField=null);
    }

    
    /**
     * Save a new entry
     *
     * @param  array $data
     * @return int|string
     */
    public function save(array $data)
    {
        Tdxio_Log::info($data,'prima di chiamare exist');
        $data['role']=$data['user_id']; //temporaneo
        
        if(!$this->exist($data)){//se non esiste già un privilegio analogo
            $table  = $this->_getTable();               
            $new_id=$table->insert($data);
            return $new_id; 
        }
        return null;        
    }

    
    public function delete(array $id_list, array $attr_value)
    {   
        $table=$this->_getTable();
        if (!empty($id_list)){
            foreach($id_list as $key=>$id){
                $where = $table->getAdapter()->quoteInto('id = ?', $id);
                $table->delete($where);
            }
            Tdxio_Log::info($id_list,'lista degli id');
        }
        if(!empty($attr_value)){
            foreach($attr_value as $attr => $value){    
                $where = $table->getAdapter()->quoteInto($attr.' = ?', $value);
                $table->delete($where);
            }
        }
    }
    
    public function exist($privilege){
        
        if((!is_null($privilege['work_id'])) && (!is_null($privilege['user_id']))){
            if($this->userIsAuthor($privilege['user_id'],$privilege['work_id']))
                return true;
        }
        
        $table=$this->_getTable();
        
        if(is_null($privilege['user_id'])){
            Tdxio_Log::info('useridisnull');
            if(is_null($privilege['role'])){
                $select=$table->select()->where('user_id is NULL');}
            else
            {   $select=$table->select()->where('(user_id = ?', $privilege['role'])->orWhere('user_id is NULL)');}
        }else{
            if(is_null($privilege['role'])){
                $select=$table->select()->where('(user_id = ?', $privilege['user_id'])->orWhere('user_id is NULL');}
            else
            {$select=$table->select()->where('(user_id = ?', $privilege['user_id'])->orWhere('user_id = ?', $privilege['role'])->orWhere('user_id is NULL)');}
        }
        
        if(!is_null($privilege['privilege'])){
            
            if($privilege['privilege']=='read'){
                $select->where('(privilege IN (?)', $this->_includeReadList)->orWhere('privilege is NULL)');
            }else{
                $select->where('(privilege = ?', $privilege['privilege'])->orWhere('privilege is NULL)');
            }
        }
           
        if(!is_null($privilege['work_id']))
            $select->where('(work_id = ?', $privilege['work_id'])->orWhere('work_id is NULL)');
        
        if(array_key_exists('visibility',$privilege))
            if(!is_null($privilege['visibility']))
                $select->where('(visibility = ?', $privilege['visibility'])->orWhere('visibility is NULL)');
        
        Tdxio_Log::info($select->__toString(),"risultato query");
        
        $result =  $table->fetchAll($select)->toArray();
        $ret=!empty($result);

        Tdxio_Log::info($ret,"privilege existence");
        Tdxio_Log::info($result,"result");
        return $ret; 
    }
    
    public function userIsAuthor($user_name=null,$work_id)
    {
        if(is_null($user_name)){ $user_name = Tdxio_Auth::getUserName();}
        $workModel = new Model_Work();
        $result = $workModel->fetchByFields(array('id'=>$work_id, 'creator'=>$user_name));
        return !empty($result);
    }
    
    public function deleteWorkPrivileges($id){
        $table = $this->_getTable();
        $table->delete($table->getAdapter()->quoteInto('work_id = ?',$id));       
    }
    
    
}
