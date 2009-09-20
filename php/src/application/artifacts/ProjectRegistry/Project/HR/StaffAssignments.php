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
 * Project staff assignments
 *
 * @package Artifacts
 */
class theStaffAssignments extends Model_Artifact {

    /**
     * Project
     *
     * @var Model_Project
     */
    protected $_project;

    /**
     * Create artifact using project name
     *
     * @return void
     **/
    public function __construct($project) {
        $this->_project = Model_Project::findByName($project);
    }
    
    /**
     * Dispatcher
     *
     * You can call $staffAssignments->PM and you will get a list of project
     * managers (if many) or just one PM (if he/she is alone)
     *
     * @return string|array One email or an array of emails
     * @throws theStaffAssignment_RoleNotFound
     **/
    public function __get($name) {
        $list = $this->_project->getStakeholdersByRole($name);
        
        // if nothing found - throw an exception
        if (!count($list))
            FaZend_Exception::raise('theStaffAssignment_RoleNotFound', 
                "Role $name is not found in project {$this->_project->name}");
                
        // if just one email - return it as string
        if (count($list) == 1)
            return array_pop($list);
            
        return $list;
    }

    /**
     * Whether this project has this given role?
     *
     * If anybody is assigned to this role, the method will return true. If nobody
     * are assigned - FALSE.
     *
     * @return boolean
     **/
    public function hasRole($role) {
        return (bool)count($this->_project->getStakeholdersByRole($name));
    }    
}
