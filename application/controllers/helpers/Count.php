<?php

/**
 * Count helper
 *
 * 
 */
 
class lineCount extends Zend_Controller_Action_Helper_Abstract
{
  
	public function direct($text){
		return $this->_countRows($text);
	}
	
	protected function _countRows($text) {
        $count=0;
        $lines=explode("\n",$text);
        foreach ($lines as $line) {
            $count+=floor(strlen($line) / 50)+1;
        }
        return $count;
    }
	
}