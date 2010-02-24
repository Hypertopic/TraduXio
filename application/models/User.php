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
    

	public function update(array $data)
	{
	}
	
	public function registerUser($user)
	{
		$data = array('name'=>$user);
		$result = $this->fetchByFields($data);
		if(empty($result))
		{	
			$code = $this->save($data);
			
		}
	}
    
}
