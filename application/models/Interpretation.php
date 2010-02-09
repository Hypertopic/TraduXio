<?php 

/**
 * Interpretation model
 *
 * Represents the translation of a group of Original Sentences
 * 
 * @uses       Model_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_Interpretation extends Model_Abstract
{
	protected $_tableClass = 'Interpretation';

	
	public function create($work_id, $original_work_id){
		$table = $this->_getTable();
		$data = array(
					'work_id' => $work_id,
					'original_work_id'=> $original_work_id,
					'translation' => '',
					'from' => 0,
					'to' => $this->getLastSentenceId($original_work_id),
					);
		$id = $table->insert($data);
		
	}
	
	protected function getLastSentenceId($work_id){
		$sentenceModel = new Model_Sentence();
		return $sentenceModel->getLastSentenceId($work_id);
	}
	
	public function fetchInterpretations($work_id){
		$interpretations = $this->fetchByFields(array('work_id'=>$work_id));
		return $interpretations;		
	} 
    
	public function fetchSentencesInterpretations($original_work_id)
	{
		$interpretations = $this->fetchByFields(array('original_work_id'=>$original_work_id));
		return $interpretations;
	}
}