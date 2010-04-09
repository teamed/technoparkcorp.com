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
 * Project role
 *
 * @package Artifacts
 */
class theProjectRole implements Model_Artifact_Stateless
{

    /**
     * Staff assignments object from the project
     *
     * @var theStaffAssignments
     */
    protected $_staffAssignments;

    /**
     * Title of the role
     *
     * @var string
     */
    protected $_title;

    /**
     * Construct it
     *
     * @param theStaffAssignments The holder of this stakeholder
     * @param string The role title, alnum only
     * @return void
     */
    public function __construct(theStaffAssignments $staffAssignments, $title)
    {
        validate()->regex($title, '/^[\sa-zA-Z]+$/');
        $this->_staffAssignments = $staffAssignments;
        $this->_title = $title;
    }

    /**
     * Compares two roles
     *
     * @param theProjectRole Role to compare with
     * @return boolean
     */
    public function equalsTo(theProjectRole $role) 
    {
        return strtolower($this->_title) === strtolower($role->_title);
    }

    /**
     * Show role in string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_title;
    }

    /**
     * Get random stakeholder, if exists
     *
     * @return theStakeholder
     */
    public function random()
    {
        return $this->_staffAssignments->findRandomStakeholderByRole($this);
    }
    
}
