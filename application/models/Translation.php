<?php 

/**
 * Translation model
 *
 * Represents the translation of a group of Original Sentences
 * 
 * @uses       Model_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_Translation extends Model_Abstract
{
	protected $_tableClass = 'Interpretation';
	protected $_cutter;

	
	public function create($work_id, $original_work_id){
		$table = $this->_getTable();
		$data = array(
					'work_id' => $work_id,
					'original_work_id'=> $original_work_id,
					'translation' => '',
					'from_segment' => 0,
					'to_segment' => $this->getLastSentenceNumber($original_work_id),
					);
		$id = $table->insert($data);
		
	}
	
	public function update(array $data, $translationId) {
		$table  = $this->_getTable();
			
		// control part
		$oldBlocks = $this->fetchInterpretations($translationId);
		Tdxio_Log::info('old blocks');
		Tdxio_Log::info($oldBlocks);
		$newBlocks=$data['TranslationBlocks'];
		Tdxio_Log::info('new blocks');
		Tdxio_Log::info($newBlocks);
		// end control part
		
        foreach ($newBlocks as $block) {
			$from=$block['from_segment'];
			if (isset($oldBlocks[$from])) {
				$oldBlock=$oldBlocks[$from];
				foreach ($block as $key=>$value) {
					$oldBlock[$key]=$value;
				}
				$block=$oldBlock;
			}			
			$oldBlocks[$from]=$block;
		}
		Tdxio_Log::info($oldBlocks,'modified blocks');
		
		foreach ($oldBlocks as $block) {
    		$from=$block['from_segment'];
	        $block['work_id']=$translationId;
            $where=$table->getAdapter()->quoteInto('work_id = ? AND ',$translationId).$table->getAdapter()->quoteInto('from_segment = ?',$from);
            Tdxio_Log::info($where,'where before');
			if ($table->fetchRow($table->select()->where($where))) {
				Tdxio_Log::info('block exists, update');
				$table->update($block,$where);
			} else {
				Tdxio_Log::info('block doesn\'t exist, create');
				$table->insert($block);
			}
        }       
	}	

	
	public function fetchTranslationWork($work_id){
		$workModel = new Model_Work();
		if(!$work = $workModel->fetchWork($work_id))
		{return null;}
		// $work['is_original_work']=false;
		$work['TranslationBlocks']=$this->fetchInterpretations($work_id);
		
		$original_work_id = $work['TranslationBlocks'][0]['original_work_id'];
		$sentenceModel= new Model_Sentence();
		$work['OriginalSentences'] = $sentenceModel->fetchSentences($original_work_id);
		$work['OriginalWorkId'] = $original_work_id;
		$work['OriginalWork'] = $workModel->fetchWork($original_work_id);
		
		$numberedSentences = array();
		foreach($work['OriginalSentences'] as $key => $sentence){
			$numberedSentences[$sentence['number']]=$sentence;
		}
		foreach($work['TranslationBlocks'] as $key => $block){
			$work['TranslationBlocks'][$key]['source'] = $this->getSourceText($block,$numberedSentences);
			$work['TranslationBlocks'][$key]['OriginalSentences']=array();
			for($i=$work['TranslationBlocks'][$key]['from_segment']; $i<=$work['TranslationBlocks'][$key]['to_segment']; $i++){
				$work['TranslationBlocks'][$key]['OriginalSentences'][$i]=$work['OriginalSentences'][$i];
			}
		}
		
		Tdxio_Log::info('traduzione');
		Tdxio_Log::alert($work);
		return $work;
	}
		
	protected function getSourceText($block,$numberedSentences=null){
		$sourceText='';
		$from = $block['from_segment'];
		$to = $block['to_segment'];
		$id = $block['original_work_id'];
		if(is_null($numberedSentences)){
			$sentenceModel= new Model_Sentence();
			$work['OriginalSentences'] = $sentenceModel->fetchSentences($id);		
			$numberedSentences = array();
			foreach($work['OriginalSentences'] as $key => $sentence){
				$numberedSentences[$sentence['number']]=$sentence;
			}			
		}
		for( $i=$from; $i<=$to; $i++){				
			$sourceText=$sourceText.$numberedSentences[$i]['content'];
		}	
		return $sourceText;
	}
	
	protected function getLastSentenceNumber($work_id){
		$sentenceModel = new Model_Sentence();
		return $sentenceModel->getLastSentenceNumber($work_id);
	}
	
	public function fetchInterpretations($work_id){
		$order='from_segment ASC';
		$interpretations = $this->fetchByFields(array('work_id'=>$work_id),$order);
		return $interpretations;		
	} 
    
	public function fetchSentencesInterpretations($original_work_id)
	{
		$order='from_segment ASC';
		$interpretations = $this->fetchByFields(array('original_work_id'=>$original_work_id),$order);
		Tdxio_Log::info($interpretations,'interp...');
		$translations = array();
		
		if(!empty($interpretations)){
			foreach($interpretations as $key=>$interp){
				$translations[$interp['work_id']][]=$interp;
			}
			$ids = array_keys($translations);
			
			Tdxio_Log::info($ids,'ids...');
			$modelWork = new Model_Work();
			$works = $modelWork->fetchWork($ids);

			foreach($works as $key=>$work){
				$translations[$work['id']]['work']=$work;			
			}
		}
		Tdxio_Log::info($translations,'tr...');
		return $translations;
	}
	
	public function cut($transId,$segnum) {
        $table=$this->_getTable();
        $condition=$table->getAdapter()->quoteInto('work_id = ?',$transId).
            " AND ".$table->getAdapter()->quoteInto('from_segment <= ?',$segnum).
            " AND ".$table->getAdapter()->quoteInto('to_segment > ?',$segnum);
        $select=$table->select()->where($condition);
        $block=$table->fetchRow($select);
        if ($block) {
            $block=$block->toArray();
            Tdxio_Log::info($block,"block found");
            $segBegin=$block['from_segment'];
            $segEnd=$block['to_segment'];
			$block['source'] = $this->getSourceText($block);
            $txtSrc=$block['source'];
            $txtTrans=$block['translation'];
            $cutter=$this->_getCutter();
            $origParts=$cutter->cutText($txtSrc,$segnum-$segBegin+1);
            $transParts=$cutter->cutText($txtTrans,$segnum-$segBegin+1);
            $block2=$block1=$block;
            $block1['to_segment']=$segnum;
            $block2['from_segment']=$segnum+1;
            //$block1['source']=$origParts[0];
            //$block2['source']=$origParts[1];
            $block1['translation']=$transParts[0];
            $block2['translation']=$transParts[1];
            $updateCond=$table->getAdapter()->quoteInto('work_id = ? AND ',$transId).$table->getAdapter()->quoteInto('from_segment = ?',$segBegin);
            Tdxio_Log::info($block1,"block1");
            Tdxio_Log::info($block2,"block2");
            Tdxio_Log::info($updateCond);
            $table->update($block1,$updateCond);
            $table->insert($block2);
        } else {
            Tdxio_Log::error("ERROR, couldn't find block containing segment $segnum",__FUNCTION__);
        }

    }
	
	public function merge($transId,$segnum) {
        $table=$this->_getTable();
        $condition1=$table->getAdapter()->quoteInto('work_id = ?',$transId).
            " AND ".$table->getAdapter()->quoteInto('to_segment = ?',$segnum);
        $condition2=$table->getAdapter()->quoteInto('work_id = ?',$transId).
            " AND ".$table->getAdapter()->quoteInto('from_segment = ?',$segnum+1);
        $select1=$table->select()->where($condition1);
        $select2=$table->select()->where($condition2);
        $block1=$table->fetchRow($select1);
        $block2=$table->fetchRow($select2);
        if ($block1 && $block2) {
            $block1=$block1->toArray();
            $block2=$block2->toArray();
            $deleteCond=$condition2;
            $cutter=$this->_getCutter();
            $newBlock=$block1;
            $newBlock['to_segment']=$block2['to_segment'];
            //$newBlock['source']=$cutter->mergeTexts($block1['source'],$block2['source']);
            $newBlock['translation']=$cutter->mergeTexts($block1['translation'],$block2['translation']);
            $updateCond=$table->getAdapter()->quoteInto('work_id = ? AND ',$transId).$table->getAdapter()->quoteInto('from_segment = ?',$block1['from_segment']);
            $table->delete($deleteCond);
            $table->update($newBlock,$updateCond);
        } else {
            Tdxio_Log::error("ERROR, couldn't merge on position $segnum",__FUNCTION__);
        }
    }

	
	
    protected function _getCutter() {
        if (null === $this->_cutter) {
            $this->_cutter = new Tdxio_Cutter();
        }
        return $this->_cutter;
    }
    

}
