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
 * One user
 *
 * @package Model
 */
class Model_User extends FaZend_StdObject {

    /**
     * Current user email
     *
     * @var Model_User
     */
    protected static $_currentUser = null;

    /**
     * Session var cache
     *
     * @var Zend_Session_Namespace
     */
    protected static $_session = null;

    /**
     * The email of the user
     *
     * @var string
     */
    protected $_email;
    
    /**
     * Construct the object
     *
     * @return void
     **/
    protected function __construct($email) {
        $this->_email = $email;
    }

    /**
     * Returns current user (email)
     *
     * @return Model_User
     * @throw FaZend_User_NotLoggedIn
     */
    public static function me() {
        return self::getCurrentUser();
    }
    
    /**
     * Returns current user
     *
     * @return Model_User
     * @throw FaZend_User_NotLoggedIn
     */
    public static function getCurrentUser () {
        if (!isset(self::$_currentUser))
            FaZend_Exception::raise('FaZend_User_NotLoggedIn', 'user is not logged in');

        return self::$_currentUser;    
    }

    /**
     * Set current user
     *
     * @param string Email of the user
     * @return void
     */
    public static function logIn($email) {
        self::$_currentUser = new Model_User($email);
        self::_session()->user = $email;
    }

    /**
     * Is it logged in?
     *
     * @return boolean
     */
    public static function isLoggedIn() {
        if (isset(self::$_currentUser) && (bool)self::$_currentUser)
            return true;

        if (self::_session()->user) {
            try {
                Model_User::setCurrentUserByEmail(self::_session()->user);
                return true;
            } catch (Shared_User_NotFoundException $e) {
                return false;
            }
        }

    }

    /**
     * Get email
     *
     * @return string
     **/
    public function _getEmail() {
        return $this->_email;
    }

    /**
     * Get session var
     *
     * @return Zend_Session_Namespace
     */
    protected static function _session() {
        if (!isset(self::$_session)) {
            if (defined('CLI_ENVIRONMENT'))
                self::$_session = new FaZend_StdObject();
            else
                self::$_session = new Zend_Session_Namespace('panel2');
        }
        return self::$_session;
    }
    
}
