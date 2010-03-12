<?php

/**
 * Tag table data gateway
 *
 * @uses       Model_Db_Table_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_DbTable_Tag extends Model_DbTable_Abstract
{
    /**
     * @var string Name of the database table
     */
    protected $_name = 'tag';
    
    public function update($data){
		if(isset($data['comment']))
		{
			Tdxio_Log::info('Tag-if');
			$where[] = $this->getAdapter()->quoteInto('comment = ?', $data['comment']);
			$where[] = $this->getAdapter()->quoteInto('taggable = ?', $data['taggable']);
			parent::update(array('multiplicity'=> new Zend_Db_Expr('multiplicity +1')),$where);
		}		
	}
	
}
