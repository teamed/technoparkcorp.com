<?php
/**
 * thePanel v2.0, Project Management Software Toolkit
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are PROHIBITED without prior written permission from 
 * the author. This product may NOT be used anywhere and on any computer 
 * except the server platform of TechnoPark Corp. located at 
 * www.technoparkcorp.com. If you received this code occasionally and 
 * without intent to use it, please report this incident to the author 
 * by email: privacy@technoparkcorp.com or by mail: 
 * 568 Ninth Street South 202, Naples, Florida 34102, USA
 * tel. +1 (239) 935 5429
 *
 * @author Yegor Bugayenko <egor@tpc2.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Access resolver
 *
 * @package Model
 * @see PanelController
 */
class Model_Auth_Resolver implements Zend_Auth_Adapter_Http_Resolver_Interface
{

    /**
     * Email of the user which tried to login last time
     *
     * @var string
     */
    protected $_email;

    /**
     * Password ID in a row
     *
     * @var integer
     */
    protected $_id;

    /**
     * Get user password
     *
     * @param  string Username
     * @param  string Authentication Realm (ignored in the method)
     * @return string|false User's shared secret, if the user is found in the
     *         realm, false otherwise.
     */
    public function resolve($username, $realm) 
    {
        /** 
         * get full list of all stakeholders from all projects
         * together with their passwords. it's going to be an 
         * associative array, with keys as emails and values as
         * arrays of passwords
         *
         * @see Shared_Project for better details
         */
        try {
            $users = Shared_Project::retrieveAllStakeholders();
        } catch (Shared_Project_EmptyDueToSoapFailure $e) {
            FaZend_Log::err('Auth resolve failure: ' . $e->getMessage());
            return false;
        }

        // user not found in general
        if (!isset($users[$username]))
            return false;

        // new user for us?
        if ($username != $this->_email) {
            $this->_email = $username;
            $this->_id = false;
        }

        // end of list of passwords?
        if (!$this->_id)
            $this->_id = count($users[$username]);

        // sanity check - we should have now at least one password in the list
        if (!$this->_id)
            return false;

        // move the index to the next available password
        $this->_id -= 1;

        return $users[$this->_email][$this->_id];
    }

    /**
     * More passwords for this user?
     *
     * @return boolean
     */
    public function hasMore() 
    {
        return (bool)$this->_id;
    }

}
