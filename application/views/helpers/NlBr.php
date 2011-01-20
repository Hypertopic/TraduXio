<?php

require_once "TdxioViewHelper.php";

class Zend_View_Helper_NlBr extends TdxioViewHelper
{
    function nlBr($text)
    {
        $text= nl2br($this->view->escape($text));
        $text=preg_replace('/{\*\*}(.*?){\*\*}/','<span class="search-highlight">\1</span>',$text);
        return $text;
    }
}
