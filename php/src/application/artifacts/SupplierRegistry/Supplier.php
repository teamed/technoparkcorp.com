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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * One supplier
 *
 * @package Artifacts
 */
class theSupplier
{

    /**
     * Email
     *
     * @var string
     */
    protected $_email;
    
    /**
     * Full name
     *
     * @var string
     */
    protected $_name = 'unknown';
    
    /**
     * Rate
     *
     * @var FaZend_Bo_Money
     */
    protected $_rate;
    
    /**
     * List of skills
     *
     * @var string[]
     */
    protected $_skills = array();
    
    /**
     * List of roles
     *
     * @var string[]
     */
    protected $_roles = array();
    
    /**
     * Construct the class
     *
     * @param string Email
     * @return void
     */
    public function __construct($email) 
    {
        $this->setEmail($email);
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     **/
    public function __get($name) 
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise(
            'Supplier_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
    /**
     * Set email
     *
     * @param string Email
     * @return void
     **/
    public function setEmail($email) 
    {
        validate()
            ->emailAddress($email, array(), "Invalid format of supplier's email: {$email}");
        $this->_email = $email;
        return $this;
    }

    /**
     * Set rate
     *
     * @param FaZend_Bo_Money Rate
     * @return void
     **/
    public function setRate(FaZend_Bo_Money $rate) 
    {
        $this->_rate = $rate;
        return $this;
    }

    /**
     * Set name
     *
     * @param string Full name of supplier
     * @return void
     **/
    public function setName($name) 
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Add skill
     *
     * @param string Skill provided
     * @return void
     **/
    public function addSkill($skill) 
    {
        if (!$this->hasSkill($skill))
            $this->_skills[] = $skill;
        return $this;
    }

    /**
     * Add role
     *
     * @param string Role to add
     * @return void
     **/
    public function addRole($role) 
    {
        if (!$this->hasRole($role))
            $this->_roles[] = $role;
        return $this;
    }
    
    /**
     * The supplier has this role
     *
     * @param string Role
     * @return boolean
     **/
    public function hasRole($role) 
    {
        return in_array($role, $this->_roles);
    }

    /**
     * The supplier has this skill?
     *
     * @param string Skill
     * @return boolean
     **/
    public function hasSkill($skill) 
    {
        return in_array($skill, $this->_skills);
    }

    /**
     * How compliant he is to the given skill
     *
     * @param string Skill
     * @return integer 0..100
     **/
    public function getCompliance($skill) 
    {
        return $this->hasSkill($skill) * 100;
    }
    
}
