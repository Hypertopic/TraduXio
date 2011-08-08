<?php
/***
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 **/

class Model_DbTable_Abstract extends Zend_Db_Table_Abstract {


	public $idcol='id';
    public $contentcol='name';
	
	
	function getName() {return $this->_name;}
	
    /**
     * Insert new row
     *
     * Ensure that a timestamp is set for the created field.
     *
     * @param  array $data
     * @return int
     */
    public function insert(array $data)
    {
        $data['created'] = date('Y-m-d H:i:s');
        Tdxio_Log::info($data);
        return parent::insert($this->cleanData($data));
    }

    /**
     * Override updating
     *
     * Ensure a timestamp is set for the modified field.
     *
     * @param  array $data
     * @param  mixed $where
     * @return void
     * @throws Exception
     */
    public function update(array $data, $where)
    {
        $data['modified'] = date('Y-m-d H:i:s');
        Tdxio_Log::info('data to update in '.$this->_name);
        Tdxio_Log::info($data);
        return parent::update($this->cleanData($data),$where);
    }

    /**
     * Clean insert/update data
     *
     * Ensure only field from the table is included in the data array.
     *
     * @param  array $data
     * @return array
     * @throws Exception
     */
    protected function cleanData($data,$includeKeys=false) {
        $fields = $this->info(Zend_Db_Table_Abstract::METADATA);
        Tdxio_Log::info($this->info());
        if (!is_array($this->idcol))
            $keys=array($this->idcol);
        else $keys=$this->idcol;
        foreach ($data as $field => $value) {
            if (!array_key_exists($field, $fields)
            || ($includeKeys && in_array($field,$keys))) {
                Tdxio_Log::info($data[$field],"column $field was found in data, but isn't part of $this->_name table");
                unset($data[$field]);
            } else {
                Tdxio_Log::info($fields[$field]['NULLABLE'],"$field nullable");
                Tdxio_Log::info(in_array($fields[$field]['DATA_TYPE'],array('text','varchar')),"$field is text");
                if ($value==='') {
                    if ($fields[$field]['NULLABLE']) {
                        $data[$field]=null;
                        Tdxio_Log::info("null empty value");
                    } else if (!in_array($fields[$field]['DATA_TYPE'],array('text','varchar'))) {
                        Tdxio_Log::info("unset empty value");
                        unset($data[$field]);
                    }
                }
            }
        }
        Tdxio_Log::info($data,"cleaned data");
        return $data;
    }
	

}

?>
