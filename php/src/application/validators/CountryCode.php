<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * Is it an ISO-3166 country code?
 *
 * @package Validate
 */
class Validator_CountryCode extends Zend_Validate_Abstract {

    const INVALID = 'invalid';

    /**
     * List of error messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "Invalid ISO 3166 country code provided",
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid ISO 3166 country code
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value) {
        $countries = Zend_Locale::getTranslationList('territory', null, 2);
        
        if (!isset($countries[$value])) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}
