<?php

class Zend_View_Helper_MakeUrl
{
    function makeUrl($url,$absolute=false)
    {
        // -10 : to get rid of [/index.php]
        $base_url = substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],"/index.php"));

        $url=$base_url.$url;
        if ($absolute) {
            $url=$_SERVER['HTTP_HOST'].$url;
            $url=str_replace('//','/',$url);
            $url="http://".$url;
        } else {
            $url=str_replace('//','/',$url);
        }
        return $url;
    }
}
