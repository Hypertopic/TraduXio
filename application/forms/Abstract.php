<?php
// application/forms/Tdxio.php
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

/**
 * This is the text edit form.  It is in its own directory in the application
 * structure because it represents a "composite asset" in your application.  By
 * "composite", it is meant that the form encompasses several aspects of the
 * application: it handles part of the display logic (view), it also handles
 * validation and filtering (controller and model).
 */

class Form_Abstract extends Zend_Form
{
    protected $_logger=null;
    public function log($message='',$title=null,$priority=1) {
        if (!$this->_logger) {
            global $logger;
            $this->_logger=$logger;
        }
        if (is_null($message)) {
            $message="{NULL}";
        } elseif (is_bool($message)) {
            $message="{".($message ? 'TRUE':'FALSE')."}";
        } elseif (is_array($message) || is_object($message)) {
            $message=print_r($message,true);
        }
        if (null !== $title) $message="[$title] : ".$message;
        $message=
        $this->_logger->log($message,$priority);
    }

}

