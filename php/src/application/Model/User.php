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
     */
    public static function setSession($session) 
    {
        self::$_session = $session;
    }
    
    /**
     * Construct the object
     *
     * @return void
     */
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
            FaZend_Exception::raise(
                'FaZend_User_NotLoggedIn', 
                'User is not logged in'
            );
        }
        return FaZend_Flyweight::factory('Model_User', $email);    
    }

    /**
     * Set current user
     *
     * @param string Email of the user
     * @return void
     * @throws Exception
     */
    public static function logIn($email) 
    {
        validate()
            ->emailAddress($email, array(), "Invalid email provided: '{$email}'");
            
        self::_session()->email = $email;
        
        // set current user in POS
        FaZend_Pos_Properties::setUserId($email);

        // set cookie properly
        Zend_Session::rememberMe();
    }
    
    /**
     * Log out everybody
     *
     * @return void
     * @throws FaZend_User_NotLoggedIn
     */
    public static function logOut() 
    {
        if (!self::isLoggedIn()) {
            FaZend_Exception::raise(
                'FaZend_User_NotLoggedIn', 
                'User is not logged in, cannot logout'
            );
        }
        
        // remove it from session
        unset(self::_session()->email);
        
        // remove cookie properly
        Zend_Session::forgetMe();
    }

    /**
     * Is it logged in?
     *
     * @return boolean
     */
    public static function isLoggedIn() 
    {
        try {
            self::getCurrentUser();
        } catch (FaZend_User_NotLoggedIn $e) {
            return false;
        }
        return true;
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of the variable
     * @return string
     * @throws Model_User_InvalidPropertyException
     */
    public function __get($name) 
    {
        if ($name == 'email') {
            return $this->_email;
        }
        FaZend_Exception::raise(
            'Model_User_InvalidPropertyException', 
            "Property is not found: '{$name}'"
        );
    }
    
    /**
     * Create and return session
     *
     * @return Zend_Session_Namespace
     */
    protected static function _session() 
    {
        if (is_null(self::$_session)) {
            self::$_session = new Zend_Session_Namespace('panel2');
        }
        return self::$_session;
    }

}
