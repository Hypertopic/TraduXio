<?php 

/**
 * Sentence model
 *
 * Represents an Original Sentence of an Original Work
 * 
 * @uses       Model_Taggable
 * @package    Traduxio
 * @subpackage Model
 */
class Model_Sentence extends Model_Taggable
{
    protected $_tableClass = 'Sentence';
    
    public function bulkSave($work_id,$segments,$clean=false) {
        if ($clean) {
            $this->cleanText($work_id);
        }
        $lastsegment=max(array_keys($segments));
        Tdxio_Log::info('lastsegment: '.$lastsegment);
        if(!preg_match("/\n$/D",$segments[$lastsegment])){
            $segments[$lastsegment].="\n";
        }
        foreach ($segments as $i=>$segment) {
            $data=array('content'=>$segment);
            if ($clean || !$this->fetchSentence($work_id,$i)) {
                Tdxio_Log::info('segmento '.$i);
                $data['work_id']=$work_id;
                $data['number']=$i;
                $this->save($data);
                Tdxio_Log::info($data,'inserted sentence '.$i.' of work '.$work_id);
            } else {
                $this->update($data,$work_id,$i);
                Tdxio_Log::info($data,'updated sentence '.$i.' of work '.$work_id);
            }
        }
    }   
    
    public function fetchSentence($work_id, $number){
        $fields = array('work_id' => $work_id, 'number' => $number);
        $sentence = $this->fetchByFields($fields);
        
        Tdxio_Log::info($sentence,'sentence,frase');
        return $sentence;       
    }
    
    public function cleanText($work_id)
    {
        $table=$this->_getTable();
        $where=$table->getAdapter()->quoteInto('work_id = ?',$work_id);
        return $table->delete($where);
    }

    public function update($data,$work_id,$number) {
        $table=$this->_getTable();
        $table->update($data,$table->getAdapter()->quoteInto('work_id = ? AND ',$work_id).$table->getAdapter()->quoteInto('number = ?',$segnum));
    }
    
    public function getLastSentenceNumber($work_id){
        $table = $this->_getTable();
        $where=$table->getAdapter()->quoteInto('work_id = ?', $work_id);
        $result = $table->fetchAll($where);
        $max = 0;
        foreach($result as $key => $row){
            $max = max($max,$row['number']);
        }
        Tdxio_Log::alert('Last index for work_id %d is '.$max,$work_id);
        return $max;
        
    }
    
    public function fetchSentences($work_id,$from=null,$to=null){
        $order='number ASC';
        $conds=array('work_id'=>$work_id);
        if ($from) {
            $conds['number >=']=$from;
        }
        if ($to) { 
            $conds['number <=']=$to; 
        }
        $sentences = $this->fetchByFields($conds,'number',$order);
        return $sentences;      
    }
    
    public function deleteSentences($id){
        $table = $this->_getTable();
        $table->delete($table->getAdapter()->quoteInto('work_id = ?',$id));  
    }
}
