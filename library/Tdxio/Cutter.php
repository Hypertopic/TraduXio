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

class Tdxio_Cutter{

    protected $_cutRegExp='/([.;?!…]+\s)([^\r\n.;?!…])|((?:\r?\n)+)(.)/';
    protected $_mergeRegExp='/[.;?!…\n](\s*)$/';
    protected $_cutSign="{{cut_here}}";

    private $_cutPos=null;
    private $_targetPos=null;

    public function cutText($text,$pos) {
        //$text=preg_replace('/(\r?\n)*$/','',$text);
        $this->_cutPos=0;
        $this->_targetPos=$pos;
        $text=preg_replace_callback($this->_cutRegExp,array($this,'_cutTextMatch'),$text);
        $parts=explode($this->_cutSign,$text);
        if (!isset($parts[1])) {
            $parts[]="";
        }
        Tdxio_Log::info($text,"text to cut on pos ".$this->_targetPos);
        Tdxio_Log::info($parts,"two parts");
        return $parts;
    }

    public function mergeTexts($text1,$text2) {
        if ($text1!=='' && !preg_match($this->_mergeRegExp,$text1)) {
        //if(!in_array(substr($text1,-1),array(".","?","!","\n","…",";"))) {
            $text1.="\n";
        }
        return $text1.$text2;
    }

    protected function _cutTextMatch($matches) {
        if (++$this->_cutPos==$this->_targetPos) {
            $i=1;
            while (isset($matches[$i])) {
                $prefix.=$matches[$i];
                $suffix.=$matches[$i+1];
                $i+=2;
            }
            return $prefix.$this->_cutSign.$suffix;
        } else {
            return $matches[0];
        }
    }

    public function getSentences($text) {
        $text=$this->sentencesSigns($text);
        $sentences=explode('{{}}',$text);
        return $sentences;
    }

    public function sentencesSigns($text) {
        $text=preg_replace('/(\r?\n)*$/','',$text);
        $this->_cutPos=0;
        return preg_replace_callback($this->_cutRegExp,array($this,'_sentencesMatch'),$text);
    }

    protected function _sentencesMatch($matches) {
        $this->_cutPos++;
        $i=1;
        $prefix=$suffix="";
        while (isset($matches[$i])) {
            $prefix.=$matches[$i];
            $suffix.=$matches[$i+1];
            $i+=2;
        }

        return $prefix."{{}}".$suffix;

    }

}

