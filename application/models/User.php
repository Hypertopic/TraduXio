<?php

/**
 * User model
 *
 * Represents a single user entry.
 * 
 * @uses       Model_Taggable
 * @package    Traduxio
 * @subpackage Model
 */
class Model_User extends Model_Taggable
{
	protected $_tableClass = 'User';
    
	/**
	* Save a new entry
	*
	* @param  array $data
	* @return int|string
	*/
	 
	public function save(array $data)
	{	
		$table  = $this->_getTable();
		if (isset($data['#content'])) {
			$data[$table->contentcol]=$data['#content'];
			unset($data['#content']);
		}
		$new_id=$table->insert($data);
		return $new_id; 
	}

	public function update(array $data,$id)
	{
	}
	
	public function registerUser($user)
	{
		$user = $data['name'];
			$fields = array('name'=>$user);
			if(is_empty($this->fetchByFields($fields)))
			{	$this->save($user);}
		
	}
    
}
