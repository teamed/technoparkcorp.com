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
 * One request for staff
 *
 * @package Artifacts
 */
class theStaffRequest {

    /**
     * Project
     *
     * @var theProject
     */
    protected $_project;
    
    /**
     * Role in the project
     *
     * @var theProjectRole
     */
    protected $_role;
    
    /**
     * List of skills required
     *
     * @var theSupplierSkill[]
     */
    protected $_skills = array();
    
    /**
     * List of activities
     *
     * @var theActivity[]
     */
    protected $_activities = array();
    
    /**
     * Quality threshold we can accept
     *
     * @var integer
     */
    protected $_threshold = 75;

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     **/
    public function __get($name) {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise('StaffRequest_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this));
    }
    
    /**
     * Set project
     *
     * @param theProject Project which requires staff
     * @return void
     **/
    public function setProject(theProject $project) {
        $this->_project = $project;
        return $this;
    }

    /**
     * Set role
     *
     * @param theProjectRole Project which requires staff
     * @return void
     **/
    public function setRole(theProjectRole $role) {
        $this->_role = $role;
        return $this;
    }

    /**
     * Set threshold
     *
     * @param integer Threshold
     * @return void
     **/
    public function setThreshold($threshold) {
        validate()
            ->type($threshold, 'integer', "Threshold must be INTEGER")
            ->true($threshold <= 100 && $threshold >= 0, "Threshold must be in [0..100] interval, {$threshold} provided");
        $this->_threshold = $threshold;
        return $this;
    }

    /**
     * Add skill
     *
     * @param theSupplierSkill Skill required, with grade
     * @return void
     **/
    public function addSkill(theSupplierSkill $skill) {
        $this->_skills[] = $skill;
        return $this;
    }

    /**
     * Add activity
     *
     * @param theActivity Activity that is planned for this person
     * @return void
     **/
    public function addActivity(theActivity $activity) {
        $this->_activity[] = $activity;
        return $this;
    }

}
