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
        foreach ($this->ps()->parent->metrics as $metric)
            $this->_findWorkPackage($metric->name);
    }
    
    /**
     * WBS is loaded?
     *
     * @return boolean
     **/
    public function isLoaded() {
        return (bool)count($this);
    }
    
    /**
     * Get WP even if it doesn't exist in array
     *
     * @return theWorkPackage
     **/
    public function offsetGet($name) {
        $wp = $this->_findWorkPackage($name);
        if (is_null($wp)) {
            FaZend_Exception::raise('WorkPackageAbsent', 
                "Metric '{$name}' does not have a work package in " . get_class($metric) . "::getWorkPackage()");
        }
        return $wp;
    }

    /**
     * Summarize work packages and return their cummulative cost
     *
     * @param array|string Name or list of names - regular expressions
     * @return Model_Cost
     **/
    public function sum($regexs) {
        if (!is_array($regexs))
            $regexs = array($regexs);
            
        $sum = new Model_Cost();
        foreach ($regexs as $regex) {
            foreach ($this->ps()->parent->metrics as $metric) {
                if (!preg_match('/' . $regex . '/', $metric->name))
                    continue;
                $wp = $this->_findWorkPackage($metric->name);
                if ($wp)
                    $sum->add($wp->cost);
                
            }
        }
        return $sum;
    }
    
    /**
     * Get WP or return NULL
     *
     * @param string Name of the work package
     * @return theWorkPackage
     **/
    protected function _findWorkPackage($name) {
        $wps = $this->getArrayCopy();
        if (isset($wps[$name])) {
            return $wps[$name];
        }

        $metric = $this->ps()->parent->metrics[$name];
        $wp = $metric->getWorkPackage();
        if ($wp)
            $this->_attachItem($wp->code, $wp, 'setWbs');
        return $wp;
    }

}
