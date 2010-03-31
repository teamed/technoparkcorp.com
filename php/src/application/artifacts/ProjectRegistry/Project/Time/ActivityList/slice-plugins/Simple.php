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

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/slice-plugins/Abstract.php';

/**
 * Simple plugin, nothing to do
 * 
 * @package Slice_Plugin
 */
class Slice_Plugin_Simple extends Slice_Plugin_Abstract
{

    /**
     * WP code
     *
     * @var theWorkPackage
     */
    protected $_wp;

    /**
     * Set name of the holder
     *
     * @return void
     */
    public function setWp(theWorkPackage $wp)
    {
        $this->_wp = $wp;
        return $this;
    }

    /**
     * Delete one activity
     *
     * @param theActivity Activity to delete
     * @return void
     */
    public final function delete(theActivity $toKill)
    {
        $this->_activities->delete($toKill);
    }
    
    /**
     * Create one new activity
     *
     * @param string Code of new activity
     * @return theActivity
     */
    public final function add($code)
    {
        if (!isset($this->_wp))
            FaZend_Exception::raise('SimplePluginInvalidInitialization');
            
        $activity = theActivity::factory($this->_activities, $this->_wp->code, $code);
        $this->_activities->add($activity);
        return $activity;
    }
    
    /**
     * Create one new milestone
     *
     * @param string Code of new milestone
     * @return theMilestone
     */
    public final function addMilestone($code)
    {
        if (!isset($this->_wp))
            FaZend_Exception::raise('SimplePluginInvalidInitialization');
            
        $milestone = theMilestone::factoryMilestone($this->_activities, $this->_wp->code, $code);
        $this->_activities->add($milestone);
        return $milestone;
    }
    
    /**
     * What activities in the global list are here, in this slice?
     *
     * @param theActivity Activity to check
     * @return boolean
     */
    protected function _isInside(theActivity $activity)
    {
        if (!isset($this->_wp))
            return true;
        return $activity->belongsTo($this->_wp);
    }
        
}
