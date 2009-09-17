<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Validate type
 *
 * @package Model
 */
class Model_Artifact_Validator_Type extends Model_Artifact_Validator_Abstract {

    /**
     * Validator
     *
     * @param string Type name
     * @return boolean
     * @throws Model_Artifact_Validator_Type_InvalidType
     */
    public function validate($type) {
        switch (strtolower($type)) {
            case 'string':
                return is_string($this->_subject);
            case 'integer':
            case 'int':
                return is_integer($this->_subject);
            default:
                FaZend_Exception::raise('Model_Artifact_Validator_Type_InvalidType',
                    "Type '{$type}' is unknown");
        }
    }

}
