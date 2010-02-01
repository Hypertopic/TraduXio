<?php

/**
 * Work model
 *
 * Utilizes the Data Mapper pattern to persist data. Represents a single 
 * work entry.
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

	protected function _getCutter() {
        if (null === $this->_cutter) {
           $this->_cutter = new Tdxio_Cutter();
        }
        return $this->_cutter;
    }


}
