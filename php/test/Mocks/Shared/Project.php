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
 * Mocked shared project
 *
 * @package test
 */
class Mocks_Shared_Project extends Shared_Project
{
    
    const NAME = 'test';

    /**
     * Mocked project
     *
     * @var Mocks_Shared_Project
     */
    protected static $_project;

    /**
     * Get test project
     *
     * @return Shared_Project
     **/
    public static function get() 
    {
        if (isset(self::$_project))
            return self::$_project;
            
        $user = Mocks_Shared_User::get();
        return self::$_project = Shared_Project::create(1, self::NAME, $user, 
            // authz
            '[' . self::NAME. ":/]\n" . 
            "{$user->email} = rw\n" . 
            "yegor256@yahoo.com = rw\n", 
            // passwd
            "[users]\n" . 
            "{$user->email} = hUjfh9Kjfl0K\n" . 
            "yegor256@yahoo.com = aF41Atlz\n", 
            // trac.ini
            '');
    }
    
    /**
     * Set this project to live connection
     *
     * @return void
     **/
    public static function setLive() 
    {
        self::$_project->user = Mocks_Shared_User::getLive();
    }

    /**
     * Set this project to test connection (no real connection)
     *
     * @return void
     **/
    public static function setTest() 
    {
        self::$_project->user = Mocks_Shared_User::get();
    }

}

