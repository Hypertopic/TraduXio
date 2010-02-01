<?php 

/**
 * Sentence model
 *
 * Utilizes the Data Mapper pattern to persist data. Represents a single 
 * work entry.
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
			$fields = array('work_id' => $work_id, 'number' => $i);
            if ($clean || !$this->fetchEntryByFields($fields)) {
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
	 /**
     * Fetch an individual entry
     *
     * @param  int|string $id
     * @return null|array
     */
    // public function fetchEntry($work_id,$number)
    // {
        // $table = $this->_getTable();
        // $select = $table->select()->where('work_id = ?', $work_id)->where('number = ?',$number);
        // $result = $table->fetchRow($select);
        // if ($result) $result=$this->_toArray($result);
        // return $result;
    // }

}
