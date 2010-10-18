<?php
// application/models/Language.php
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

/**
 * This model class represents the business logic associated with a "guestbook"
 * model.  While its easy to say that models are generally derived from
 * database tables, this is not always the case.  Data sources for models are
 * commonly web services, the filesystem, caching systems, and more.  That
 * said, for the purposes of this guestbook applicaiton, we have split the
 * buisness logic from its datasource (the dbTable).
 *
 * This particular class follows the Table Module pattern.  There are other
 * patterns you might want to employ when modeling for your application, but
 * for the purposes of this example application, this is the best choice.
 * To understand different Modeling Paradigms:
 *
 * @see http://martinfowler.com/eaaCatalog/tableModule.html [Table Module]
 * @see http://martinfowler.com/eaaCatalog/ [See Domain Logic Patterns and Data Source Arch. Patterns]
 */

class Model_Language extends Model_Abstract {

    protected $_tableClass='Language';


    /**
     * Fetch all entries
     *
     * @return array
     */
    public function fetchEntries()
    {
        // we are gonna return just an array of the data since
        // we are abstracting the datasource from the application,
        // at current, only our model will be aware of how to manipulate
        // the data source (dbTable).
        
        $db=$this->_getTable()->getAdapter();
        $select=$db->select();
        $select->from(array('i'=>'languages'),array('id','ref_name'))->joinLeft(array('w'=>'work'), 'i.id=w.language','count(w.id)')->order(array('count(w.language) DESC','i.ref_name'))->group(array('i.id', 'i.ref_name'))->where('i.active = ?', true);
        return $db->query($select)->fetchAll();
    }

    /**
     * Return an array usable as options for a select;
     *
     * @return Array
     *
     */
    public function fetchOptions()
    {
        $options=Array();

        foreach ($this->fetchEntries() AS $k => $v) {
            $key = $v['id']; // obtain value of partner column
            if(!isset($options[$key])) {
                $options[$key] = __($key);
            }
        }
        return $options;
    }
    
    
    public function getBrowserLang(){
        $table = $this->_getTable();
        $lang_reg = new Zend_Locale(Zend_Locale::BROWSER);
        $lang2 = substr($lang_reg,0,2);
        //$select = $table->select()->from($table,'id')->where('part1 = ?',$lang2);
        $result = $this->fetchByFields(array('part1'=>$lang2));
        Tdxio_Log::info($result[0],'browserLang 3');
        return $result[0];        
    }

}

