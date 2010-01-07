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
 * Total number of defects
 * 
 * @package Artifacts
 */
class Metric_Artifacts_Defects_Total extends Metric_Abstract
{

    /**
     * Forwarders
     *
     * @var array
     * @see Metric_Abstract::$_patterns
     */
    protected $_patterns = array(
        '/byReporter\/(.*?)/' => 'byReporter',
        '/byComponent\/(.*?)/' => 'byComponent',
        '/byOwner\/(.*?)/' => 'byOwner',
        '/bySeverity\/(.*?)/' => 'bySeverity',
        '/byMilestone\/(.*?)/' => 'byMilestone',
        '/byStatus\/(.*?)/' => 'byStatus',
        );

    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload()
    {
        $this->_value = 1;
        
        // go to the metric required
        foreach (new RegexIterator(new ArrayIterator($this->_patterns), '/^by\w+$/') as $pattern) {
            if (is_null($this->_getOption($pattern)))
                continue;

            $method = '_reload' . ucfirst($pattern);
            if (!method_exists($this, $method))
                FaZend_Exception::raise('Metric_Artifact_Defects_Total_InvalidClass',
                    "Method '$method' is not implemented, why?");
                    
            return $this->$method($this->_getOption($pattern));
        }
        
        $project = $this->_project->fzProject();
        $asset = $project->getAsset(Model_Project::ASSET_DEFECTS);
        $tickets = $asset->retrieveBy();
        $this->_value = count($tickets);
        
        // load all kid metrics
        foreach (new RegexIterator(new ArrayIterator($this->_patterns), '/^by\w+$/') as $pattern) {
            $method = '_ping' . ucfirst($pattern);
            if (!method_exists($this, $method))
                FaZend_Exception::raise('Metric_Artifact_Defects_Total_InvalidClass',
                    "Method '$method' is not implemented, why?");
            $this->$method();
        }
    }
    
    /**
     * Ping all patterns by all possible reporters
     *
     * @return void
     **/
    protected function _pingByReporter() 
    {
        foreach ($this->_project->staffAssignments as $stakeholder)
            $this->_pingPattern('byReporter/' . $stakeholder->email);
    }
        
    /**
     * Ping all patterns by all possible defect owners
     *
     * @return void
     **/
    protected function _pingByOwner() 
    {
        foreach ($this->_project->staffAssignments as $stakeholder)
            $this->_pingPattern('byOwner/' . $stakeholder->email);
    }
        
    /**
     * Ping all patterns by all possible ticket severities
     *
     * @return void
     **/
    protected function _pingBySeverity() 
    {
        foreach ($this->_project->fzProject()->getAsset(Model_Project::ASSET_DEFECTS)->getSeverities() as $severity)
            $this->_pingPattern('bySeverity/' . $severity);
    }
        
    /**
     * Ping all patterns by all possible ticket severities
     *
     * @return void
     **/
    protected function _pingByStatus() 
    {
        foreach ($this->_project->fzProject()->getAsset(Model_Project::ASSET_DEFECTS)->getStatuses() as $status)
            $this->_pingPattern('byStatus/' . $status);
    }
        
    /**
     * Ping all patterns by all possible ticket severities
     *
     * @return void
     **/
    protected function _pingByMilestone() 
    {
        foreach ($this->_project->milestones as $milestone)
            $this->_pingPattern('byMilestone/' . $milestone->name);
    }
        
    /**
     * Ping all patterns by all possible components
     *
     * @return void
     **/
    protected function _pingByComponent() 
    {
        //
    }
        
    /**
     * Reload the metric by the given reporter
     *
     * @param string Reporter's email
     * @return void
     **/
    protected function _reloadByReporter($reporter) 
    {
        // todo
        $this->_value = 1;
    }
        
    /**
     * Reload the metric by the given owner
     *
     * @param string Owner's email
     * @return void
     **/
    protected function _reloadByOwner($owner) 
    {
        // todo
        $this->_value = 1;
    }
        
    /**
     * Reload the metric by the given component
     *
     * @param string Name of the component
     * @return void
     **/
    protected function _reloadByComponent($component) 
    {
        // todo
        $this->_value = 1;
    }
        
    /**
     * Reload the metric by the given milestone
     *
     * @param string Name of milestone
     * @return void
     **/
    protected function _reloadByMilestone($milestone) 
    {
        // todo
        $this->_value = 1;
    }
        
    /**
     * Reload the metric by the given severity
     *
     * @param string Name of severity
     * @return void
     **/
    protected function _reloadBySeverity($severity) 
    {
        // todo
        $this->_value = 1;
    }
        
    /**
     * Reload the metric by the given status
     *
     * @param string Name of the status
     * @return void
     **/
    protected function _reloadByStatus($status) 
    {
        // todo
        $this->_value = 1;
    }
        
}
