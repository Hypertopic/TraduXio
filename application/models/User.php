<?php

/**
 * User model
 *
 * Represents a single user entry.
 * 
 * @uses       Model_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_User extends Model_Abstract
{
    protected $_tableClass = 'User';
//    public $_defaultOptions = array();

        
    public function registerUser($user,$options=null)
    {
        Tdxio_Log::info('flusso: 2 USER REGISTERUSER');
        $data = array('name'=>$user);
        if(!$this->entryExists($data))
        {
            $code = $this->save($data);
        }else{
            $data = array('last_access'=>date('Y-m-d H:i:s'));
            if(!is_null($options)){
                $data['options'] = serialize($options);
            }
            $table = $this->_getTable();
            $where = $table->getAdapter()->quoteInto('name = ?',$user);
            $table->update($data,$where);
        }
    }
    
    public function getOptions($user,$opt_name=null){
        Tdxio_Log::info('flusso: 3 USER GETOPTIONS');
        $options = array();
        $table = $this->_getTable();
        $select = $table->select()->from($table,'options')->where('name = ?',$user);
        $result = $table->fetchAll($select)->toArray();
        if(!empty($result)){
            $options = unserialize($result[0]['options']);
        }
        Tdxio_Log::alert($result,'result');
        Tdxio_Log::alert($options,'get options');
        
        return $options;
               
    }
    
    
    public function setOptions($user,array $options){
        Tdxio_Log::info('flusso: 4 USER SETOPTIONS');
        $newoptions = Zend_Registry::get('preferences');
        Tdxio_Log::info($newoptions,'hello kitty');
        foreach($options as $key=> $opt){
            if(!empty($opt)){
                Tdxio_Log::info($opt,'option key');
                $newoptions[$key] = $opt;
            }
        }   
        $this->registerUser($user,$newoptions);
        
    }
    
    
}
