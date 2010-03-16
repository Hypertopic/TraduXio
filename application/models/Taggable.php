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

    protected $_tableClass = 'Taggable';
    
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
    
    public function deleteTag($username,$taggableId,$tag){
        $tagTable = new Model_DbTable_Tag();
        if(!(is_null($username)||is_null($taggableId)||is_null($tag))){
            $where[] = $tagTable->getAdapter()->quoteInto('"user" = ?',$username);
            $where[] = $tagTable->getAdapter()->quoteInto('taggable = ?',$taggableId);
            $where[] = $tagTable->getAdapter()->quoteInto('"comment" = ?',$tag);
            Tdxio_Log::info($where,'whereee');
            return $tagTable->delete($where);
           
        }
        return 'not enough parameters to delete a tag';           
    }
    

    public function normalizeTags($tags){
        
        $ntags = array();
        foreach($tags as $key=>$tag){
            $ntags[$tag['comment']][]=$tag;            
        }
        Tdxio_Log::info($ntags,'normalizedtags1');
        $maxMult=1;
        foreach($ntags as $key=>$tag){
            $count = count($ntags[$key]);
            Tdxio_Log::info($count,'count'.$key);
            $ntags[$key]['multiplicity']=$count;
            $maxMult = max($maxMult,$count);
        }
        
        Tdxio_Log::info($ntags,'normalizedtags2');
        foreach($ntags as $key => $tag){
            $ntags[$key]['multiplicity']/=$maxMult;
        }
        return $ntags;
    }
}
