<?php 

// library/Tdxio/Model/Abstract.php
/***
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 **/
 
class Model_Abstract {

	protected $_table;
	
	public function __construct($class_name=null,$idField=null,$contentField=null) {
        if (!is_null($class_name)) {
			
			Tdxio_Log::info($class_name,'class_name_construct');
            $this->_table = new Model_DbTable_Abstract(strtolower($class_name),$idField,$contentField);
        }
    }
	
	protected function _getTable() {
        if (null === $this->_table) {
			$classname='Model_DbTable_' . $this->_tableClass;
            $this->_table = new $classname;
        }
        return $this->_table;
    }
	
	
	public function save(array $data)
    {
        $table  = $this->_getTable();
        $new_id = $table->insert($data);
        return $new_id;
    }
	
	public function fetchAll() {
		$table=$this->_getTable();
		return $table->fetchAll();
	}
	
	public function fetchEntryByFields(array $fields){
		if(!is_null($fields)){
		$table = $this->_getTable();
		$select = $table->select();
		foreach($fields as $fieldname => $value){
			$select->where($fieldname . ' = ?', $value);
		}
		Tdxio_Log::info($select);
        $result = $table->fetchRow($select);
        if ($result) $result=$this->_toArray($result);
		}
        return $result;
	}
	
}