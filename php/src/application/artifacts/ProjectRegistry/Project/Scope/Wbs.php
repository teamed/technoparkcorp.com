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
 * Project work breakdown structure, collection of work packages
 *
 * @package Artifacts
 */
class theWbs extends Model_Artifact_Bag implements Model_Artifact_Passive {
    
    /**
     * Returns a list of activities to be done now, according to current WBS
     *
     * @return theActivities
     **/
    public function getActivities() {
        $activities = new theActivities();
        $splitter = new theActivitySplitter($this, $activities);
        foreach ($this as $wp) {
            $splitter->dispatch($wp);
        }
        return $activities;
    }
    
    /**
     * Load work packages into the WBS, before iteration
     *
     * @return void
     **/
    public function reload() {
        // clear all existing WPs
        foreach ($this as $key=>$metric)
            unset($this[$key]);
        
        // add new from metrics
        foreach ($this->ps()->parent->metrics as $metric) {
            $wp = $metric->getWorkPackage();
            if (!is_null($wp))
                $this->_attachItem($wp->code, $wp, 'setWbs');
        }
    }
    
    /**
     * WBS is loaded?
     *
     * @return boolean
     **/
    public function isLoaded() {
        return (bool)count($this);
    }
    
}
