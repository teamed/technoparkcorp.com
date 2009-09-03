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
 * Access resolver
 *
 * @package Model
 */
class Model_Auth_Resolver implements Zend_Auth_Adapter_Http_Resolver_Interface {

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
     * @param  string $username Username
     * @param  string $realm    Authentication Realm
     * @throws Zend_Auth_Adapter_Http_Resolver_Exception
     * @return string|false User's shared secret, if the user is found in the
     *         realm, false otherwise.
     */
    public function resolve($username, $realm) {

        $users = Shared_Project::retrieveAllStakeholders();

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
        $this->_id--;

        return $users[$this->_email][$this->_id];

    }

    /**
     * More passwords for this user?
     *
     * @return boolean
     */
    public function hasMore() {
        return (bool)$this->_id;
    }

}
