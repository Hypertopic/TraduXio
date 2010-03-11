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
	
	
	public $_dbPrivilegeList=array('read','edit','translate','manage');
	public $_dbPermissions=array('true','false');
    public $_contrary_id=array();
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

    public function update(array $data,$id)
    {
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
	
	

/*	public function findResourceIdsByUserId($userid,$privilege)
	{
	// per il momento é un modo veloce per far funzionare le cose
	// in seguito si dovrebbe accedere al db e prendere le stesse 
	// informazioni dalla tabella privileges
	
		$table=$this->_getTable(); 
		
		$select=$table->select()->from($table,'work_id')->where('(user_id = ?', $userid)->orWhere('user_id is NULL)')->where('(privilege = ?', $privilege)->orWhere('privilege is NULL)');
		Tdxio_Log::info('selezione');Tdxio_Log::info($select->__toString());
		
		$ids = $table->fetchAll($select)->toArray();
		
		Tdxio_Log::info($ids);
			
		return $ids;
	}*/
	
	public function exist($privilege){
		
		if(!is_null($privilege['work_id']) && !is_null($privilege['user_id'])){
			if($this->userIsAuthor($privilege['user_id'],$privilege['work_id']))
				return true;
		}
		
		$table=$this->_getTable();
		
		if(is_null($privilege['user_id'])){
			Tdxio_Log::info('useridisnull');
			if(is_null($privilege['role'])){
				$select=$table->select()->where('user_id is NULL');}
			else
			{	$select=$table->select()->where('(user_id = ?', $privilege['role'])->orWhere('user_id is NULL)');}
		}else{
			if(is_null($privilege['role'])){
				$select=$table->select()->where('(user_id = ?', $privilege['user_id'])->orWhere('user_id is NULL');}
			else
			{$select=$table->select()->where('(user_id = ?', $privilege['user_id'])->orWhere('user_id = ?', $privilege['role'])->orWhere('user_id is NULL)');}
		}
		
		if(!is_null($privilege['privilege'])){
			$select->where('(privilege = ?', $privilege['privilege'])->orWhere('privilege is NULL)');
		}
		if(!is_null($privilege['work_id'])){
			if($privilege['visibility']=='custom'){
				$select->where('(work_id = ?', $privilege['work_id'])->orWhere('work_id is NULL)');
				$select->where('visibility = ?', $privilege['visibility']);
			}elseif($privilege['visibility']=='public' or $privilege['visibility']=='private')
			$select->where('visibility = ?', $privilege['visibility']);
			//$select->where('work_id is NULL');
		}
		
		Tdxio_Log::info($select->__toString(),"risultato query");
		
		$result =  $table->fetchAll($select)->toArray();
		$ret=!empty($result);

		Tdxio_Log::info($ret,"privilege existence");
		Tdxio_Log::info($result,"result");
		return $ret; 
	}
	
	public function userIsAuthor($user_name,$work_id)
	{
		$workModel = new Model_Work();
		$result = $workModel->fetchByFields(array('id'=>$work_id, 'creator'=>$user_name));
		return !empty($result);
	}
	
	
	
}
