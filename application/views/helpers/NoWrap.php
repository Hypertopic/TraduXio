<?php

require_once "TdxioViewHelper.php";

class Zend_View_Helper_NoWrap extends TdxioViewHelper
{
    function noWrap($text)
    {
        return preg_replace("/\s+/","&nbsp;",$this->view->escape($text));
    }
}
