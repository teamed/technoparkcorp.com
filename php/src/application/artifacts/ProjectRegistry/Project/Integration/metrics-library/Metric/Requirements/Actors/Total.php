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
 * Total number of actors
 * 
 * @package Artifacts
 */
class Metric_Requirements_Actors_Total extends Metric_Abstract {

    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload() {
        $this->_value = 1;
    }
        
    /**
     * Get work package
     *
     * @return theWorkPackage
     **/
    public function getWorkPackage() {
        return $this->_makeWp(
            $this->_project->wbs->sum('requirements\/functional\/total')->multiply(0.2), 
            'Specify actors');
    }
        
    /**
     * Split the WP onto activities
     *
     * @param Slice_Plugin_Abstract
     * @return void
     **/
    protected function _split(Slice_Plugin_Abstract $slice) {
        
        // split the activity onto smaller pieces
        $total = $slice->iterate('downCurve', array(
            'sow' => 'Identify actors',
            'maxCost' => '50 USD',
            'minCost' => '10 USD'));
return;
        // attach them to milestones
        foreach ($slice->sector(1, null) as $i=>$activity) {
            $slice->afterMilestone($activity)
                ->when('[readiness] > ?', 0.3 * $i / $total);
        }
            
        // assign exit criteria
        foreach ($slice->onlyActivities() as $activity) {
            theActivityCriteria::factory($activity)
                ->when('[requirements/actors/compliance] > ?', $slice->sumUntil($activity)->divide($slice->sum()));
        }
    }
        
}