<?php

require_once "TdxioViewHelper.php";

class Zend_View_Helper_LimitText extends TdxioViewHelper
{
    public function limitText($text,$length=100)
    {
        $output=substr($text, 0, $length);
        if ($length<=strlen($text)) $output.="...";
        $output=nl2br($this->view->escape($output));
        return $output;
    }

}
