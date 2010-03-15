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
    
    
    public function tag($tag){
        
        $tagTable = new Model_DbTable_Tag;
        $data = array('taggable' => $tag['taggable_id'],
                      'user' => $tag['username'],
                      //'genre' => $tag['comment'],
                      'comment' => $tag['comment']
                    );
        Tdxio_Log::info($data);
        $select = $tagTable->select()->where('"comment" = ?',$data['comment'])->where('taggable = ? ',$data['taggable'])->where('"user" = ?',$data['user']);
        Tdxio_Log::info($select->__toString(),'selecttostring');
        $result = $tagTable->fetchRow($select);
        Tdxio_Log::info($result,'bidibodo');
        if(!empty($result)){
        }else{
            $newId = $tagTable->insert($data);
        }
        
    }   
    
    public function deleteTag($tagId,$taggableId=null){
        $tagTable = new Model_DbTable_Tag();
        $where[] = $tagTable->getAdapter()->quoteInto('id = ?',$tagId);
        if(!is_null($taggableId)){
            $where[] = $tagTable->getAdapter()->quoteInto('taggable = ?',$taggableId);
        }
        $tagTable->delete($where);
    }
}
