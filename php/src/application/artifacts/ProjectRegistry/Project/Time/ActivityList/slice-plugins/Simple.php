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
 * Simple plugin, nothing to do
 * 
 * @package Slice_Plugin
 */
class Slice_Plugin_Simple extends Slice_Plugin_Abstract {

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
     **/
    public function setWp(theWorkPackage $wp) {
        $this->_wp = $wp;
        return $this;
    }

    /**
     * Delete one activity
     *
     * @param theActivity Activity to delete
     * @return void
     **/
    public final function delete(theActivity $toKill) {
        foreach ($this->_activities as $key=>$activity) {
            if ($activity->equalsTo($toKill)) {
                unset($this->_activities[$key]);
            }
        }
    }
    
    /**
     * Create one new activity
     *
     * @param string Code of new activity
     * @return theActivity
     **/
    public final function add($code) {
        $activity = theActivity::factory($this->_activities, $this->_wp->code, $code);
        $this->_activities[] = $activity;
        return $activity;
    }
    
    /**
     * What activities in the global list are here, in this slice?
     *
     * @param theActivity Activity to check
     * @return boolean
     **/
    protected function _isInside(theActivity $activity) {
        return $activity->belongsTo($this->_wp);
    }
        
}
