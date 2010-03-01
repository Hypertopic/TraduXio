<?php

/**
 * Work model
 *
 * Represents a single work entry.
 * 
 * @uses       Model_Taggable
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
		$translationModel = new Model_Translation();
		$translationModel->create($work_id, $original_work_id);
		return $work_id;
	}

	protected function _getCutter() {
		if (null === $this->_cutter) {
			$this->_cutter = new Tdxio_Cutter();
		}
		return $this->_cutter;
	}

	
	public function fetchWork($id){
		return $this->fetchEntry($id);
	}
	
	public function fetchOriginalWork($work_id){
	
		if(!$work = $this->fetchWork($work_id)){return null;}
		$sentenceModel= new Model_Sentence();
		$work['Sentences'] = $sentenceModel->fetchSentences($work_id);
		$content = '';
		foreach($work['Sentences'] as $key => $sentence){
			$content.=$sentence['content'];
		}
		$work['the_text']=$content;
		$translationModel = new Model_Translation();
		$work['Interpretations'] = $translationModel->fetchSentencesInterpretations($work_id);	
		return $work;
	}

	public function fetchAllOriginalWorks()
	{
		$table = $this->_getTable();
		$select1 = $this->getSelectCondOriginalWork('sentence');
		$select2 = $table->select()->where('id IN (?)',$select1);
		Tdxio_Log::info('stringa sql '.$select2->__toString());
		return $table->fetchAll($select2);
	}
	
	public function getSelectCondOriginalWork($table_name){
		$table = $this->_getTable();
		$db = $table->getAdapter();
		$select = $db->select()->distinct()->from($table_name,'work_id');
		
		return $select;		
	}
	
	public function fetchMyTranslations($user){
		$table = $this->_getTable();
		$select1 = $this->getSelectCondOriginalWork('interpretation');
		$select2 = $table->select()->where('id IN (?)',$select1)->where('creator = ?',$user);
		return	$table->fetchAll($select2);
	}
	
	public function isOriginalWork($id)
	{
	
	}



}
