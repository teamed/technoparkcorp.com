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
 * Group of activities
 *
 * @package Artifacts
 */
class theActivities extends ArrayIterator implements Model_Artifact_Stateless, Model_Artifact_Passive {

    /**
     * Project, the owner of this list
     *
     * @var theProject
     */
    protected $_project;

    /**
     * Set project
     *
     * @return void
     **/
    public function setActivityList(theActivityList $list) {
        $this->_project = $list->ps()->parent;
    }

    /**
     * Reload it
     *
     * @return void
     **/
    public function reload() {
        // kill all existing activities
        foreach ($this as $key=>$value)
            unset($this[$key]);
            
        // ask all work packages to add their activities
        foreach ($this->_project->wbs as $wp) {
            $wp->split($this);
            // logg('WP ' . $wp->code . ' added ' . count($this));
        }
    }

    /**
     * Is it loaded?
     *
     * @return boolean
     **/
    public function isLoaded() {
        return (bool)count($this);
    }
    
    /**
     * Get project
     *
     * @return theProject
     **/
    public function getProject() {
        return $this->_project;
    }
    
    /**
     * Get new slice
     *
     * @return Slice_Plugin_Simple
     **/
    public function getSlice() {
        require_once dirname(__FILE__) . '/slice-plugins/Abstract.php';
        return Slice_Plugin_Abstract::factory('simple', $this);
    }

    /**
     * Get new slice, with only activites from this WP
     *
     * @param theWorkPackage Work package to narrow activities to
     * @return Slice_Plugin_Simple
     **/
    public function getSliceByWp(theWorkPackage $wp) {
        return $this->getSlice()->setWp($wp);
    }
    
    /**
     * Find one activity by given ID
     *
     * @param string Alnum ID of the activity
     * @return theActivity
     **/
    public function findById($id) {
        return $this->findByName(Model_Pages_Encoder::decode($id));
    }

    /**
     * Find one activity by given name
     *
     * @param string Name of the activity
     * @return theActivity
     **/
    public function findByName($name) {
        foreach ($this as $activity)
            if ($activity->name == $name)
                return $activity;
        FaZend_Exception::raise('ActivityNotFound', 'Activity not found with name: ' . $name);
    }

    /**
     * Add one activity
     *
     * @param theActivity Activity to add
     * @return theActivity
     **/
    public final function add(theActivity $activity) {
        $this[] = $activity;
        return $activity;
    }
    
    /**
     * Delete one activity
     *
     * @param theActivity Activity to delete
     * @return void
     **/
    public final function delete(theActivity $toKill) {
        foreach ($this as $key=>$activity) {
            if ($activity->equalsTo($toKill)) {
                unset($this[$key]);
            }
        }
    }
    
}
