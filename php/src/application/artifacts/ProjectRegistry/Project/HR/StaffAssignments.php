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
 * Project staff assignments
 *
 * It's an associative array (it behaves like one), where keys are emails
 * and values are instances of theStakeholder class.
 *
 * @package Artifacts
 */
class theStaffAssignments implements ArrayAccess, Countable, Iterator, Model_Artifact_Stateless
{

    /**
     * The holder of this staff assignments
     *
     * @var theProject
     */
    public $project;
    
    /**
     * Collection of stakeholders
     *
     * @var theStakeholder[]
     **/
    protected $_stakeholders;

    /**
     * Dispatcher
     *
     * You can call $staffAssignments->PM and you will get a list of project
     * managers (if many) or just one PM (if he/she is alone)
     *
     * @return theProjectRole|theStakeholder One stakeholder or one role
     * @throws Exception If the role is not found
     **/
    public function __get($name) 
    {
        $list = $this->_project()->getStakeholdersByRole($name);
        
        // if nothing found - throw an exception
        validate()->true(count($list) > 0, 
            "Role '{$name}' is not found in project '{$this->_project()->name}'");
                
        // if just one email - return it as string
        if (count($list) == 1)
            return FaZend_Flyweight::factory('theStakeholder', $this, array_pop($list));
            
        return $this->createRole($name);
    }

    /**
     * Whether this project has this given role?
     *
     * If anybody is assigned to this role, the method will return true. If nobody
     * are assigned - FALSE.
     *
     * @return boolean
     **/
    public function hasRole($role) 
    {
        return (bool)count($this->_project()->getStakeholdersByRole($role));
    }    
    
    /**
     * Get one stakeholder by role
     *
     * @return theStakeholder One stakeholder for this role
     * @throws Exception If the role is not found
     **/
    public function findRandomStakeholderByRole(theProjectRole $role) 
    {
        $list = $this->_project()->getStakeholdersByRole((string)$role);
        
        // if nothing found - throw an exception
        validate()->true(count($list) > 0, "Role '{$role}' is not found in project '{$this->_project()->name}'");
                
        return FaZend_Flyweight::factory('theStakeholder', $this, array_pop($list));
    }

    /**
     * Get list of roles for the given person
     *
     * @param theStakeholder The person
     * @return theProjectRole[] List of roles
     **/
    public function retrieveRolesByStakeholder(theStakeholder $person) 
    {
        $roles = $this->_project()->getRolesByStakeholder((string)$person);
        foreach ($roles as &$role)
            $role = $this->createRole($role);
        return $roles;
    }
    
    /**
     * Get list of stakeholders for the given role
     *
     * @param theProjectRole The role
     * @return theStakeholder[] List of stakeholders
     **/
    public function retrieveStakeholdersByRole(theProjectRole $role) 
    {
        $emails = $this->_project()->getStakeholdersByRole((string)$role);
        foreach ($emails as &$email)
            $email = $this[$email];
        return $emails;
    }
    
    /**
     * Create project role object
     *
     * @return theProjectRole
     **/
    public function createRole($name) 
    {
        return FaZend_Flyweight::factory('theProjectRole', $this, $name);
    }
    
    /**
     * Get stakeholder which is LOGGED IN now
     *
     * @return theStakeholder
     **/
    public function getActiveStakeholder() 
    {
        return $this[Model_User::getCurrentUser()->email];
    }
    
    /**
     * Get Model_Project object
     *
     * @return Model_Project
     **/
    protected function _project() 
    {
        return $this->project->fzProject();
    }
    
    /**
    * Iterator interface required method
     *
     * @return void
     **/
    public function rewind() 
    {
        return $this->_getStakeholders()->rewind();
    }
    
    /**
    * Iterator interface required method
     *
     * @return void
     **/
    public function key() 
    {
        return $this->_getStakeholders()->key();
    }
    
    /**
    * Iterator interface required method
     *
     * @return void
     **/
    public function next() 
    {
        return $this->_getStakeholders()->next();
    }
    
    /**
     * Iterator interface required method
     *
     * @return void
     **/
    public function valid() 
    {
        return $this->_getStakeholders()->valid();
    }
    
    /**
     * Iterator interface required method
     *
     * @return void
     **/
    public function current() 
    {
        return $this->_getStakeholders()->current();
    }
    
    /**
     * Countable method
     *
     * @return void
     **/
    public function count() 
    {
        return $this->_getStakeholders()->count();
    }
    
    /**
     * ArrayAccess method
     *
     * @return void
     **/
    public function offsetGet($name) 
    {
        validate()->emailAddress($name, array(), "Email is wrong: '{$name}'");
        return $this->_getStakeholders()->offsetGet($name);
    }
    
    /**
     * ArrayAccess method
     *
     * @return void
     **/
    public function offsetSet($name, $value) 
    {
        FaZend_Exception::raise(
            'StaffAssignmentsAreStatic',
            "You can't change staffAssignments directly, only through FaZend"
        );
    }
    
    /**
     * ArrayAccess method
     *
     * @return void
     **/
    public function offsetUnset($name) 
    {
        FaZend_Exception::raise(
            'StaffAssignmentsAreStatic',
            "You can't change staffAssignments directly, only through FaZend"
        );
    }
    
    /**
     * ArrayAccess method
     *
     * @return void
     **/
    public function offsetExists($name) 
    {
        validate()->emailAddress($name, array(), "Email is wrong: '{$name}'");
        return $this->_getStakeholders()->offsetExists($name);
    }
    
    /**
     * Reload list of stakeholders
     *
     * @return ArrayIterator
     **/
    protected function _getStakeholders() 
    {
        if (!isset($this->_stakeholders)) {
            $this->_stakeholders = new ArrayIterator();
            foreach (array_keys($this->_project()->getStakeholders()) as $email)
                $this->_stakeholders[$email] = FaZend_Flyweight::factory('theStakeholder', $this, $email);
        }
        return $this->_stakeholders;
    }    
    
}
