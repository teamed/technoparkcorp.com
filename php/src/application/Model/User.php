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
class Model_User
{

    /**
     * Session namespace
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
     * Set session namespace explicitly
     *
     * @param mixed Session to use
     * @return void
     **/
    public static function setSession($session) 
    {
        self::$_session = $session;
    }
    
    /**
     * Construct the object
     *
     * @return void
     **/
    public function __construct($email) 
    {
        $this->_email = $email;
    }

    /**
     * Returns current user (email)
     *
     * @return Model_User
     * @throws FaZend_User_NotLoggedIn
     */
    public static function me() 
    {
        return self::getCurrentUser();
    }
    
    /**
     * Returns current user
     *
     * @return Model_User
     * @throws FaZend_User_NotLoggedIn
     */
    public static function getCurrentUser() 
    {
        $email = self::_session()->email;
        if (!$email) {
            FaZend_Exception::raise('FaZend_User_NotLoggedIn', 
                'User is not logged in');
        }
        return FaZend_Flyweight::factory('Model_User', $email);    
    }

    /**
     * Set current user
     *
     * @param string Email of the user
     * @return void
     */
    public static function logIn($email) 
    {
        self::_session()->email = $email;
        
        // set current user in POS
        FaZend_Pos_Properties::setUserId($email);
    }

    /**
     * Is it logged in?
     *
     * @return boolean
     */
    public static function isLoggedIn() 
    {
        $email = self::_session()->email;
        if ($email)
            self::logIn($email);
        return (bool)$email;
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of the variable
     * @return string
     **/
    public function __get($name) 
    {
        if ($name == 'email')
            return $this->_email;
    }
    
    /**
     * Create and return session
     *
     * @return Zend_Session_Namespace
     **/
    protected static function _session() 
    {
        if (is_null(self::$_session))
            self::$_session = new Zend_Session_Namespace('panel2');
        return self::$_session;
    }

}
