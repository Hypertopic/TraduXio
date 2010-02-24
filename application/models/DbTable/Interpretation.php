<?php

/**
 * Interpretation table data gateway
 *
 * @uses       Model_Db_Table_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_DbTable_Interpretation extends Model_DbTable_Abstract
{
    /**
     * @var string Name of the database table
     */
    protected $_name = 'interpretation';
	
	public function insert(array $data){
		if(isset($data['id'])){
			unset ($data['id']);
		}
		return parent::insert($this->cleanData($data));
	}
}
