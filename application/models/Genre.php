<?php

/**
 * Genre model
 *
 * Represents a single genre entry.
 * 
 * @uses       Model_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_Genre extends Model_Abstract
{
    protected $_tableClass = 'Genre';
     
    public function getGenres(){
        $table = $this->_getTable();
        $select = $table->select()->from($table,array('id',"name"));
        $result = $table->fetchAll($select)->toArray();        
        $genres = array();
        foreach($result as $key=>$genre){
            $genres[$genre['id']]=$genre['name'];
        }
        return $genres;
    }
    
}
