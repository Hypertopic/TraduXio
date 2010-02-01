<?php

/**
 * Taggable model
 *
 * Utilizes the Data Mapper pattern to persist data. Represents a single 
 * taggable entry.
 * 
 * @uses       Model_TaggableMapper
 * @package    Traduxio
 * @subpackage Model
 */
class Model_Taggable extends Model_Abstract
{

	public function __construct() {
        
		Tdxio_Log::info('blabla');
		//parent::__construct($class_name,$idField,$contentField); 
    }

}
