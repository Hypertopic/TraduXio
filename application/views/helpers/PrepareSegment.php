<?php
// application/views/helpers/PrepareSegment.php
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

class Zend_View_Helper_PrepareSegment extends TdxioViewHelper
{
    public function prepareSegment($segment)
    {
        $output=$this->view->escape($segment);
        //$output = preg_replace('/[\r\n\s]*$/','',$output);
        $output = nl2br($output);
        return $output;
    }

}
