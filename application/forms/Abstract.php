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
    
    protected function _getLanguages()
    {
        static $languageOptions=null;

        if (null === $languageOptions) {
            $langModel = new Model_Language();
            $languageOptions=$langModel->fetchOptions();
        }
     //   Tdxio_Log::info($languageOptions,'lang codes');
        return $languageOptions;
    }
}

