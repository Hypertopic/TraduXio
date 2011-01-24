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
    

    public function getTags($taggable_id){
        $db = $this->_getTable()->getAdapter();
        if(!is_array($taggable_id)){
            $tags = $db->fetchAll($db->select()->from('tag')->where('taggable = ?',$taggable_id));
        }else{
            $tags = $db->fetchAll($db->select()->from('tag')->where('taggable IN (?)',$taggable_id));
        }
        Tdxio_Log::info($tags,'TaggableModel getTags');
        $genreModel = new Model_Genre();
        $genres=$genreModel->getGenres();
        $result=array();
        foreach($tags as $key=>$tag){
            $tag['genre_name']=$genres[$tag['genre']];
            $result[$tag['taggable']][]=$tag;
        }
        $result['Genres']=$genres;
        Tdxio_Log::info($result,"fetched tags");
        return $result;
    }
    
    
    public function tag($tag){
        
        $tagTable = new Model_DbTable_Tag;
        $data = array('taggable' => $tag['taggable_id'],
                      'user' => $tag['username'],
                      'genre' => $tag['genre'],
                      'comment' => $tag['comment']
                    );
        Tdxio_Log::info($data,'gigigi');
        $select = $tagTable->select()
                            ->where('"comment" = ?',$data['comment'])->where('taggable = ? ',$data['taggable'])
                            //->where('"user" = ?',$data['user'])->where('genre = ? ',$data['genre']);
                            ->where('genre = ? ',$data['genre']);
        Tdxio_Log::info($select->__toString(),'selecttostring');
        $result = $tagTable->fetchRow($select);
        Tdxio_Log::info($result,'bidibodo');
        if(!empty($result)){
        }else{
            $newId = $tagTable->insert($data);
        }
        $response = array('outcome'=>true,'message'=>null);
        return $response;        //da verificare
    }   
    
    public function deleteTag($username,$taggableId,$tag,$genre){
        $tagTable = new Model_DbTable_Tag();
        if(!(is_null($username)||is_null($taggableId)||is_null($tag))){
            $where[] = $tagTable->getAdapter()->quoteInto('"user" = ?',$username);
            $where[] = $tagTable->getAdapter()->quoteInto('taggable = ?',$taggableId);
            $where[] = $tagTable->getAdapter()->quoteInto('"comment" = ?',$tag);
            $where[] = $tagTable->getAdapter()->quoteInto('genre = ?',$genre);
            Tdxio_Log::info($where,'whereee');
            $tagTable->delete($where); 
            
            $select = $tagTable->select()->where('taggable = ?',$taggableId)->where('genre=?',$genre);
            $remainingTags = $tagTable->fetchAll($select)->toArray();
            Tdxio_Log::info($remainingTags,'iriss');
            return empty($remainingTags);
                      
        }
        throw new Zend_Exception(__('Not enough parameters to delete a tag'));           
    }
    
    public function deleteAllTaggableTags($taggableId){
        $tagTable = new Model_DbTable_Tag();
        if(!(is_null($taggableId))){
            $where = $tagTable->getAdapter()->quoteInto('taggable = ?',$taggableId);
            return $tagTable->delete($where);           
        }
        throw new Zend_Exception(__('No taggable-id specified'));           
    }
    

    public function normalizeTags($tags){
      
        $newtags=array();
        foreach($tags as $key=>$tag){
            if(!isset($newtags[$tag['genre']]))
            {   $newtags[$tag['genre']]=array();}
            if(!isset($newtags[$tag['genre']][$tag['comment']]))
            {   $newtags[$tag['genre']][$tag['comment']]=array();}
            $newtags[$tag['genre']][$tag['comment']][]=$tag;                
        }
        Tdxio_Log::info($newtags,'normalized tags new');
        $maxMult=1;
        foreach($newtags as $genre=>$names){
            foreach($names as $name=>$tag){
                $count = count($names[$name]);
                Tdxio_Log::info($count,'count'.$name);
                $newtags[$genre][$name]['multiplicity']=$count;
                $maxMult = max($maxMult,$count);
            }
        }
        Tdxio_Log::info($newtags,'normalized tags new 2');
        foreach($newtags as $genre => $names){
            foreach($names as $name=>$tag){
                $newtags[$genre][$name]['multiplicity']/=$maxMult;
            }
        }
        
        Tdxio_Log::info($newtags,'normalized tags new 3');
        return $newtags; 

    /*    $ntags = array();
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
      */
    }
    
    
    public function getNewModTags($user=null){
        $tagTable = new Model_DbTable_Tag();
        $db = $tagTable->getAdapter();
        $sqlcond = "tag.created > current_date - integer '30'  OR tag.modified > current_date - integer '30' ";
        
        $select = $db->select();
        $select->distinct()->from(array('tag'=>'tag'),array('tag.*'))
                        ->join(array('g'=>'genre'),'tag.genre = g.id',array('g.name as genre_name'))
                        ->join(array('w'=>'work'),'tag.taggable = w.id',array('w.title'))
                        ->where($sqlcond);
                        
                        
        //$select = $tagTable->select()->where($sqlcond);
        if(!is_null($user)){
            $selectMine = $tagTable->getAdapter()->select()->from('work','id')->where('creator = ?',$user);
            $select->where('taggable IN (?)',$selectMine);
        }
        
        Tdxio_Log::info($select->__toString(),'NEW_tag_request_string');
        $results = $db->fetchAll($select);
        Tdxio_Log::info($results,'new tags');
        return $results;
    }
}
