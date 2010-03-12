<?php

/**
 * User model
 *
 * Represents a single user entry.
 * 
 * @uses       Model_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_User extends Model_Abstract
{
	protected $_tableClass = 'User';
    

/*	public function update(array $data)
	{
	}*/
	
	public function registerUser($user)
	{
		$data = array('name'=>$user);
		if(!$this->entryExists($data))
		{	
			$code = $this->save($data);			
		}else{
            $data = array('last_access'=>date('Y-m-d H:i:s'));
            $table = $this->_getTable();
            $where = $table->getAdapter()->quoteInto('name = ?',$user);
            $table->update($data,$where);
        }
	}
    
}
