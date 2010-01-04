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
 * One changelog field, status of the issue
 *
 * @package Model
 */
class Model_Issue_Changelog_Field_Status extends Model_Issue_Changelog_Field_Abstract {

    const OPEN = 1;
    const FIXED = 2;
    const INVALID = 3;
    
    /**
     * Validate new value
     *
     * @param mixed Value to set
     * @return void
     * @throws Exception if failed
     **/
    protected function _validate($value) {
        validate()
            ->type($value, 'integer', "Status shall be INT only")
            ->true(in_array($value, array(
                self::OPEN,
                self::FIXED,
                self::INVALID)), "Status shall be from the predefined list");
            
        return true;
    }

}
