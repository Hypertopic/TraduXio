<?php

/**
 * Taggable table data gateway
 *
 * @uses       Zend_Db_Table_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_DbTable_Taggable extends Model_DbTable_Abstract
{
    /**
     * @var string Name of the database table
     */
    protected $_name = 'taggable';
	
	protected $_sequence = 'taggable_id_seq';
}
