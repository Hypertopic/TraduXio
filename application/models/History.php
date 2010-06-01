<?php

/**
 * History model
 *
 * Represents a single history entry.
 * 
 * @uses       Model_Abstract
 * @package    Traduxio
 * @subpackage Model
 */
class Model_History extends Model_Abstract
{

    protected $_tableClass = 'History';
    public $_codeList=false;
    
    public function __construct(){
        $this->_codeList = array(
        'single'=>array(
                    0=>__('work metadata modified'), 
                    1=>__('text extended'), 
                    2=>__('translation modified'), 
                    3=>__('tag added'), 
                    4=>__('tag removed'),
                    5=>__('text created')
        ),
        'all'=>array(
                    0=>0, 
                    1=>1, 
                    2=>2, 
                    3=>3, 
                    4=>4,
                    5=>5
        ));
        
        return parent::__construct();
    }
    
    public function getHistory($id){
        if(is_null($id)) return null;
        
        $db = $this->_getTable()->getAdapter();
        $workModel = new Model_Work();
        $selectAlwd = $workModel->getSelectCondAllowedWork('read'); 
        $select = $db->select()->from('history',array('user','date','work_id','message'))->where('work_id = ?',$id)->where('work_id IN (?)',$selectAlwd)->order('date DESC');
        $rows = $db->fetchAll($select);
        $history = $this->extractInfo($rows,'single');
        $lastEvent = end($history);
        if($lastEvent['message']!=$this->_codeList['single'][5]){
            
            $work = $workModel->fetchWork($id);
            $history[]=array('user'=>$work['creator'],'date'=>$work['created'],'message'=>$this->_codeList['single'][5]);
        }
        Tdxio_Log::info($history,'history');
        return $history;
    }

    public function addHistory($id,$code,array $params=array()){
        if(is_null($id)) return;
        
        $histTable = new Model_DbTable_History();
        $user_name = Tdxio_Auth::getUserName();
        $user_name = (is_null($user_name))?__('Anonymous'):$user_name;
        $histTable->insert(array('user'=>$user_name,'work_id'=>$id),$code,$params);
    }
    
    public function getAllRecentHistory($days,$drop=false){
        $db = $this->_getTable()->getAdapter();
        $workModel = new Model_Work();
        $selectAlwd = $workModel->getSelectCondAllowedWork('read');         
        $sqlcond = "history.date > current_date - integer '".$days."'";
        $select = $db->select()->from('history',array('user','date','work_id','message'))->join('work','work.id=history.work_id')->where('history.work_id IN (?)',$selectAlwd)->where($sqlcond)->order('date DESC');
        Tdxio_Log::info($select->__toString(),'perfranck');
        $rows = $db->fetchAll($select);
        $history = $this->extractInfo($rows,'all');
        Tdxio_Log::info($history,'all recent history'); 
        if($drop)
            $history = $this->dropRedundancies($history);
        Tdxio_Log::info($history,'all recent history after');     
        return $history;
    }
    
    public function extractInfo($rows,$type){
        $history = $rows;
        $genreModel = new Model_Genre();
        $genres = $genreModel->getGenres();
        foreach($rows as $key=>$row){
            $msg=unserialize($row['message']);
            Tdxio_Log::info($row,'row');
            Tdxio_Log::info($row['message'],'unserialized msg');
            Tdxio_Log::info($msg,'unserialized msg');
            $history[$key]['message'] = $this->_codeList[$type][$msg['code']];
            if(isset($msg['params'])){
                $genre_name = __($genres[$msg['params']['genre']]);
                $history[$key]['params'] = array('genre'=>$genre_name,'tag'=>$msg['params']['tag']);
            }
        }
        return $history;        
    }
    
    
    public function dropRedundancies(array $history){
        krsort($history);//analyze history from the oldest new to the last new
        Tdxio_Log::info($history,'all recent history reordered');
        foreach($history as $key=>$row){
            if($row['message']==4)
                unset($history[$key]);//se il tag è di eliminazione di un tag, eliminarlo}
            elseif($row['message']==3){// se il tag è di aggiunta di un tag, eliminarlo se esiste anche il suo opposto 
                $droppedRow = $row;
                $droppedRow['message']=4;
                unset($droppedRow['date']);
                if(in_array($droppedRow,$history))
                    unset($history[$key]);
            }            
        }
        ksort($history);//reorder the history in the good way
        return $history;
    }
    
    public function equivalent(array $a, array $b, array $paramList){
        foreach($paramList as $param){
            if($param=='date'){
                $adate = getdate(strtotime($a['date']));
                $bdate = getdate(strtotime($b['date']));
                Tdxio_Log::info($adate,'date1');
                Tdxio_Log::info($bdate,'date2');
                $diff = array_diff(array($adate['year'],$adate['yday']),array($bdate['year'],$bdate['yday']));
                if(!empty($diff))
                    return false;
            }elseif($a[$param]!=$b[$param]){
                return false;
            }
        }
        return true;
    }
}
