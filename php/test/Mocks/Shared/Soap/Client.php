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
 * Mocked client to fazend
 *
 * @package test
 */
class Mocks_Shared_Soap_Client
{
    
    /**
     * Create instance
     *
     * @return Mocks_Shared_Soap_Client
     **/
    public static function get() 
    {
        return new self();
    }
    
    /**
     * Get list of projects
     *
     * @return array
     **/
    public function getProjects() 
    {
        return array(
            FaZend_StdObject::create()
                ->set('id', 1)
                ->set('name', Mocks_Model_Project::NAME)
                ->set('user', 1)
                ->set('authz', "[" . Mocks_Model_Project::NAME . ":/]\n" . 
                    Mocks_Model_Project::PM . " = rw\n" .
                    Model_Wobot_PM::getEmailByProjectName(Mocks_Model_Project::NAME) . " = rw\n"
                    )
                ->set('passwd', "[users]\n" . 
                    Mocks_Model_Project::PM . " = " . Mocks_Model_Project::PM_PWD . "\n" .
                    Model_Wobot_PM::getEmailByProjectName(Mocks_Model_Project::NAME) . " = test\n" 
                    )
                ->set('tracIni', ''),
            FaZend_StdObject::create()
                ->set('id', 2)
                ->set('name', 'PMO')
                ->set('user', 1)
                ->set('authz', "[PMO:/]\n" . Mocks_Model_Project::PM . " = rw\n")
                ->set('passwd', "[users]\n" . Mocks_Model_Project::PM . " = " . Mocks_Model_Project::PM_PWD . "\n")
                ->set('tracIni', ''),
            );
    }

    /**
     * Get list of users
     *
     * @return array
     **/
    public function getUsers() 
    {
        return array(
            FaZend_StdObject::create()
                ->set('id', 1)
                ->set('email', Mocks_Model_Project::PM)
                ->set('password', Mocks_Model_Project::PM_PWD),
            );
    }

}

