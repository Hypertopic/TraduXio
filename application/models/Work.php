<?php

/**
 * Work model
 *
 * Represents a single work entry.
 * 
 * @uses       Model_WorkMapper
 * @package    Traduxio
 * @subpackage Model
 */
class Model_Work extends Model_Taggable
{
	protected $_tableClass = 'Work';
    protected $_cutter;
    
	/**
     * Save a new entry
     *
     * @param  array $data
     * @return int|string
     */
	 
    public function save(array $data)
    {
		// insert data into the table "work"
        $table  = $this->_getTable();
        $new_id = $table->insert($data);
        
		// insert data into the table "sentence"
        $sentences = $this->_getCutter()->getSentences($data['the_text']);
		unset($data['the_text']);

		$sentenceModel = new Model_Sentence();
		$sentenceModel->bulkSave($new_id,$sentences,true);
        return $new_id;
    }
	
	public function createTranslation(array $data,$original_work_id)
	{
		// insert data into the table "work"
        $table  = $this->_getTable();
        $work_id = $table->insert($data);
        
		// insert data into the table "interpretation"
		$interpretationModel = new Model_Interpretation();
		$interpretationModel->create($work_id, $original_work_id);
        return $work_id;
	}

	protected function _getCutter() {
        if (null === $this->_cutter) {
           $this->_cutter = new Tdxio_Cutter();
        }
        return $this->_cutter;
    }
	
/* 	public function fetchWork($id){
		$work = $this->fetchEntry($id);
		if($work){
			$work['Sentences']=$this->fetchSentences($id);
			$content = '';
			foreach($work['Sentences'] as $key => $sentence){
				$content.=$sentence['content'];
			}
			$work['the_text']=$content;
		}		
		return $work;
	}
	 */
	
	/**
     * Retrieve all the text's informations
     *
     * @param  integer $id
     * @return array
     */

	
	/****************************
	*/
	
	public function fetchWork($id){
		return $this->fetchEntry($id);
	}
	
	public function fetchOriginalWork($work_id){
		if(!$work = $this->fetchWork($work_id)){return null;}
		$work['is_original_work']=true;
		$sentenceModel= new Model_Sentence();
		$work['Sentences'] = $sentenceModel->fetchSentences($work_id);
		$content = '';
		foreach($work['Sentences'] as $key => $sentence){
			$content.=$sentence['content'];
		}
		$work['the_text']=$content;
		$interpretationModel = new Model_Interpretation();
		$work['Interpretations'] = $interpretationModel->fetchSentencesInterpretations($work_id);	
		return $work;
	}

	public function fetchTranslationWork($work_id){
		if(!$work = $this->fetchWork($work_id))
		{return null;}
		$work['is_original_work']=false;
		$translationModel = new Model_Interpretation();
		$work['InterpretationBlocks']=$translationModel->fetchInterpretations($work_id);
		
		$original_work_id = $work['InterpretationBlocks'][0]['original_work_id'];
		$sentenceModel= new Model_Sentence();
		$work['OriginalSentences'] = $sentenceModel->fetchSentences($original_work_id);
		$work['OriginalWork'] = $this->fetchWork($original_work_id);
		
		$numberedSentences = array();
		foreach($work['OriginalSentences'] as $key => $sentence){
			$numberedSentences[$sentence['number']]=$sentence;
		}
		foreach($work['InterpretationBlocks'] as $key => $block){
			$work['InterpretationBlocks'][$key]['source'] = $this->getSourceText($block,$numberedSentences);
			$work['InterpretationBlocks'][$key]['OriginalSentences']=array();
			for($i=$work['InterpretationBlocks'][$key]['from']; $i<=$work['InterpretationBlocks'][$key]['to']; $i++){
				$work['InterpretationBlocks'][$key]['OriginalSentences'][$i]=$work['OriginalSentences'][$i];
			}
		}
		
		Tdxio_Log::info('traduzione');
		Tdxio_Log::alert($work);
		return $work;
	}
		
	protected function getSourceText($block,$numberedSentences){
		$sourceText='';
		$from = $block['from'];
		$to = $block['to'];
		$id = $block['original_work_id'];
		for( $i=$from; $i<=$to; $i++){				
			$sourceText=$sourceText.$numberedSentences[$i]['content'];
		}	
		return $sourceText;
	}
	
/* 	protected function fetchSentences($work_id){
		
		$sentenceModel = new Model_Sentence();
		$sentences = $sentenceModel->fetchByFields(array('work_id'=>$work_id),'number');
		return $sentences;		
	} */	
		
/* 	protected function fetchSentencesInterpretations($original_work_id)
	{
		$interpretationModel = new Model_Interpretation();
		$interpretations = $interpretationModel->fetchByFields(array('original_work_id'=>$original_work_id));
		return $interpretations;
	} */
	

/* 	protected function fetchInterpretations($work_id){
		$interpretationModel = new Model_Interpretation();
		$interpretations = $interpretationModel->fetchByFields(array('work_id'=>$work_id));
		return $interpretations;		
	} */
	



}
