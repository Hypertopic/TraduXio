<?php

/**
 * History table data gateway
 *
 * @uses       Zend_Db_Table_Abstract
 * @package    Traduxio
 * @subpackage Model/DbTable
 */
class Model_DbTable_History extends Zend_Db_Table_Abstract
{
    /**
     * @var string Name of the database table
     */
    protected $_name = 'history';

    public function insert($data,$code,$params=array()){    
        if(!empty($params))
            $data['message'] = serialize(array('code'=> $code,'params'=>$params));        
        else
            $data['message'] = serialize(array('code'=> $code));        
    
        return parent::insert($data);
    }

}
