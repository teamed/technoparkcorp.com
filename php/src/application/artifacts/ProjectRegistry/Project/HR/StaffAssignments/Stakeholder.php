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
 * Project stakeholder
 *
 * @package Artifacts
 * @see theStaffAssignments
 */
class theStakeholder implements Model_Artifact_Stateless
{

    /**
     * Staff assignments object from the project
     *
     * @var theStaffAssignments
     */
    protected $_staffAssignments;

    /**
     * Stakeholder's email
     *
     * @var string
     */
    protected $_email;

    /**
     * Construct it
     *
     * @param theStaffAssignments The holder of this stakeholder
     * @param string Email of the stakeholder
     * @return void
     **/
    public final function __construct(theStaffAssignments $staffAssignments, $email) 
    {
        validate()->emailAddress($email, array());
        $this->_staffAssignments = $staffAssignments;
        $this->_email = $email;
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
            'Stakeholder_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
    /**
     * Show stakeholders in string
     *
     * @return string
     **/
    public function __toString() 
    {
        return $this->email;
    }

    /**
     * This stakeholder has this role?
     *
     * @param theProjectRole Role to find
     * @return boolean
     */
    public function hasRole(theProjectRole $role) 
    {
        $roles = $this->_staffAssignments->retrieveRolesByStakeholder($this);
        foreach ($roles as $myRole) {
            if ($myRole === $role)
                return true;
        }
        return false;
    }

    /**
     * How much this supplier already get in the given project?
     *
     * @return FaZend_Bo_Money
     **/
    public function getPaidInProject(theProject $project) 
    {
        return thePayment::getPaidInProjectToStakeholder($this, $project);
    }

    /**
     * Get email
     *
     * @return string
     **/
    protected function _getEmail() 
    {
        return $this->_email;
    }

    /**
     * Get list of my roles
     *
     * @return theProjectRole[]
     **/
    protected function _getRoles() 
    {
        return $this->_staffAssignments->retrieveRolesByStakeholder($this);
    }

    /**
     * Get list of my roles, in string
     *
     * @return string
     **/
    protected function _getRolesString() 
    {
        return implode(', ', $this->roles);
    }

}
