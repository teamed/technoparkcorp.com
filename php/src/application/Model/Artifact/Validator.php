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
 * One simple validator
 *
 * @package Model
 */
class Model_Artifact_Validator {

    /**
     * Call decorator
     *
     * @param string Name of the method
     * @param array List of arguments
     * @return value
     */
    public function __call($method, array $args) {
        // first param is subject
        $subject = array_shift($args);

        // last param is message
        $message = array_pop($args);

        $class = 'Model_Artifact_Validator_' . ucfirst($method);
        $validator = new $class($subject);

        if (!call_user_func_array(array($validator, 'validate'), $args)) {
            FaZend_Exception::raise($class . '_Failure', $message . ' (' . (is_scalar($subject) ? $subject : get_class($subject)) . ')');
        }

        return $this;
    }

}
