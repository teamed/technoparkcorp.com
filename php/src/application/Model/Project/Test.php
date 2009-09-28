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
 * One project for test
 *
 * @package Model
 */
class Model_Project_Test extends Model_Project {

    const NAME = 'test';
    const PM = 'tester@tpc2.com';

    /**
     * Create and return a test project instance
     *
     * @return Model_Project_Test
     **/
    public static function make() {
        $pwd = md5(rand());
        return new Model_Project(
            1, // id
            self::NAME, // project name
            new Shared_User(1, self::PM, $pwd), // project manager
            '[' . self::NAME . ":/]\n" . self::PM . ' = rw', // authz file
            self::PM . ' = ' . $pwd // passwd file, random password
            );
    }

    /**
     * Get list of emails for a given role
     *
     * @return string[]
     */
    public function getStakeholdersByRole($role) {
        return array(Model_Project_Test::PM);
    }
    
}
