<?php
// application/views/helpers/AddCutSign.php
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

require_once "TdxioViewHelper.php";

class Zend_View_Helper_AddCutSign extends TdxioViewHelper
{
    public function addCutSign($segment,$segnum=1)
    {
        $this->_pos=1;
        $this->_segnum=$segnum;
        $output=$this->view->escape($segment);
        $output = preg_replace_callback('/((?: +)|((?:\r?\n)))$/',array($this,'_cutSign'),$output,1,$count);
		if (!$count) {
			$output=$output.$this->_cutSign();
		}
        //$output = preg_replace_callback('/\n+/',array($this,'_cutSign2'),$output);
        $output = nl2br($output);
        return $output;
    }

    private function _cutSign($matches=array('')) {
        $suffix=$matches[0];

        $insert="<span title=\"split here\" class=\"split sign\"><a href=\"". $this->view->url(array('action'=>'cut','after'=>$this->_segnum))."\">&#x21A9;</a></span>";
        $output=$insert.$suffix;
        return $output;
    }

    private $_segnum=null;

}
