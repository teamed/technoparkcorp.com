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
 * Dispatcher between WBS, WorkPackage, Splitters and Activities
 *
 * @package Artifacts
 */
class theActivitySplitter {
    
    /**
     * WBS
     *
     * @var theWbs
     */
    protected $_wbs;
    
    /**
     * Activity list to work with
     *
     * @var theActivities
     */
    protected $_activities;
    
    /**
     * Constructor
     *
     * @param theWbs Holder of this splitter
     * @param theActivities List of activities to work with
     * @return void
     **/
    public function __construct(theWbs $wbs, theActivities $activities) {
        $this->_wbs = $wbs;
        $this->_activities = $activities;
    }
    
    /**
     * Dispatch work package and retrieve activities from it
     *
     * @param theWorkPackage Work package to dispatch
     * @return void
     **/
    public function dispatch(theWorkPackage $wp) {
        $wp->split($this);
    }
        
    /**
     * Create new splitting module and split activities
     *
     * @param theWorkPackage Initiator of this procedure
     * @param string Name of the module
     * @param Zend_Config Config for the module
     * @param theActivities List of activities
     * @return void
     **/
    public function split(theWorkPackage $wp, $name, Zend_Config $config, theActivities $list) {
        $className = 'ActivitySplitter_' . ucfirst($name);
        require_once dirname(__FILE__) . '/ActivitySplitter/' . ucfirst($name) . '.php';
        $splitter = new $className($this->_wbs, $wp, $config);
        $splitter->split($list);
    }
    
    /**
     * Append this list to the main list
     *
     * @return void
     **/
    public function append(theActivities $list) {
        foreach ($list as $activity)
            $this->_activities[] = $activity;
    }
        
}
