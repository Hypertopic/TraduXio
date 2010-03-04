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
		
		if($this->exist_exact($data,true)){//cerca il privilegio opposto
			$this->delete($this->_contrary_id);
		}
		if(!$this->exist($data)){//se non esiste già un privilegio analogo
			Tdxio_Log::info('entrato qui');
			$table  = $this->_getTable();				
			$new_id=$table->insert($data);
		    return $new_id; 
		}
		return null;
		
    }

    public function update(array $data,$id)
    {
    }
	
	public function delete(array $id_list)
	{	if (!empty($id_list)){
			$table=$this->_getTable();
			foreach($id_list as $key=>$id){
			
				$where = $table->getAdapter()->quoteInto('id = ?', $id);
				$table->delete($where);
			}
			Tdxio_Log::info($id_list,'lista degli id');
		}
		
	}
	
	
	
	public function fetchEntriesByUser(array $userids)
	{	
		$null_id = null;
		$table=$this->_getTable();
		$entries = array();
		foreach($userids as $i=>$userid){
			$select=$table->select()->from($table)->where('user_id = ?',$userid);
			if(is_null($entries)){
				$entries[$i] = $table->fetchAll($select)->toArray();
			}else{
				$entries=array_merge($entries,$table->fetchAll($select)->toArray());
			}
		}
		$select=$table->select()->from($table)->where('user_id IS NULL');
		$entries=array_merge($entries,$table->fetchAll($select)->toArray());
    	Tdxio_Log::info($entries,'dududo dadada');

		return $entries;
	}
	
/*	public function findResourceIdsByUserId($userid,$privilege)
	{
	// per il momento é un modo veloce per far funzionare le cose
	// in seguito si dovrebbe accedere al db e prendere le stesse 
	// informazioni dalla tabella privileges
	
		$table=$this->_getTable(); 
		
		$select=$table->select()->from($table,'text_id')->where('(user_id = ?', $userid)->orWhere('user_id is NULL)')->where('(privilege = ?', $privilege)->orWhere('privilege is NULL)');
		Tdxio_Log::info('selezione');Tdxio_Log::info($select->__toString());
		
		$ids = $table->fetchAll($select)->toArray();
		
		Tdxio_Log::info($ids);
			
		return $ids;
	}
*/	
	
	// public function findByRole($role){

		// $table=$this->_getTable();
		// $select=$table->select()->from($table,'text_id')->where('user_id = ?',$userid);
		// $ids = $table->fetchAll($select);
		// Tdxio_Log::info($ids,'questi id');
		// return $ids;	
	// }
	
/*	public function isAllowed($userid,$role,$privilege,$textid=null){
		Tdxio_Log::info('Chiamata isAllowed PrivilegeModel');
		$table=$this->_getTable();
		$db=$table->getAdapter();
		$select=new Zend_Db_Select($db);
		Tdxio_Log::info($table,'tabella');
		// $textid = $db->quote($textid);//serve se il campo text_id é di tipo char
		

	    $select = $db->select()->from('privileges')->where('(privilege = ?', $privilege)->orWhere('privilege is NULL)')								
												   ->where('(user_id = ?', $userid)->orWhere('user_id = ?', $role)->orWhere('user_id is NULL)');

		if(!is_null($text_id)){
			$select->where('(text_id = ?', $textid)->orWhere('text_id is NULL)');
		}else{
			$select->where('text_id is NULL');
		}
							
        $result =  $db->query($select)->fetchAll();
		Tdxio_Log::info($select->__toString());
		Tdxio_Log::info($result,'risultato query isAllowed');
		return (!empty($result));		
	}	*/
	
	public function exist($privilege){
		Tdxio_Log::info($privilege,'log in exist privilegemodel');
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
		if(!is_null($privilege['text_id'])){
			$select->where('(text_id = ?', $privilege['text_id'])->orWhere('text_id is NULL)');
		}
		
		//$select->where('condition = ?',$privilege['condition']);
	
		Tdxio_Log::info($select->__toString(),"risultato query");
		
		$result =  $table->fetchAll($select)->toArray();
		$ret=!empty($result);

		// check if the text belongs to the user
		if(!$ret && !is_null($privilege['text_id']) && !is_null($privilege['user_id'])){

			$ret=$this->userIsAuthor($privilege['user_id'],$privilege['text_id']);
		}
		Tdxio_Log::info($ret,"privilege existence");
		Tdxio_Log::info($result,"result");
		return $ret; 
		
		return true;
	}
	
	public function userIsAuthor($user_name,$work_id)
	{
		$workModel = new Model_Work();
		$result = $workModel->fetchByFields(array('id'=>$work_id, 'creator'=>$user_name));
		return !empty($result);
	}
	
	public function exist_exact($privilege,$contrary=false){
		Tdxio_Log::info($privilege,'log in exist_exact privilegemodel');
		$table=$this->_getTable();
		$select_set=false;
		
		if(!is_null($privilege['user_id'])){
			$select_set=true;
			$select=$table->select()->where('user_id = ?', $privilege['user_id']);
		}
		
		if(!is_null($privilege['privilege'])){
			if($select_set)
				{$select->where('privilege = ?', $privilege['privilege']);}
			else{
				$select_set=true;
				$select=$table->select()->where('privilege = ?', $privilege['privilege']);
			}
		}
		
		if(!is_null($privilege['text_id'])){
			if($select_set)
			{$select->where('text_id = ?', $privilege['text_id']);}
			else{
				$select_set=true;
				$select=$table->select()->where('text_id = ?', $privilege['text_id']);
			}
		}
		
		if($contrary){
			$contrCond =($privilege['condition']=='true')?'false':'true';
			if($select_set)
			{$select->where('condition = ?',$contrCond);}
			else{$select=$table->select()->where('condition = ?',$contrCond);}
		}else{
			if($select_set)
			{$select->where('condition = ?',$privilege['condition']);}
			else{$select=$table->select()->where('condition = ?',$privilege['condition']);}
		}
		Tdxio_Log::info($select->__toString());
		Tdxio_Log::info('risultato contrary query ');		
		$result =  $table->fetchAll($select)->toArray();
		
		if($ret=!empty($result)){
			$this->_contrary_id[]=$result[0]['id'];
			Tdxio_Log::info($this->_contrary_id,'contrary_id');
		}
		Tdxio_Log::info($ret);
		Tdxio_Log::info($result);
		return $ret; 
		
	}
	
	
}
