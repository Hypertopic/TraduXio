<?php

/**
 * Taggable model
 *
 * Represents a single taggable entry.
 * 
 * @uses       Model_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_Taggable extends Model_Abstract
{

	public function __construct() {
        
		//parent::__construct($class_name,$idField,$contentField); 
	}

	public function getTags($taggable_id){
		$db = $this->_getTable()->getAdapter();
		if(!is_array($taggable_id)){
			$tags = $db->fetchAll($db->select()->from('tag')->where('taggable = ?',$taggable_id));
		}else{
			$tags = $db->fetchAll($db->select()->from('tag')->where('taggable IN (?)',$taggable_id));
		}
		$result=array();
		foreach($tags as $key=>$tag){
			$result[$tag['taggable']][]=$tag;
		}
		Tdxio_Log::info($result,"fetched tags");
		return $result;
	}
}
