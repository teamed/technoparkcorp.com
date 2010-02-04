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
        '/^byReporter\/(.*?)$/' => 'byReporter',
        '/^byComponent\/(.*?)$/' => 'byComponent',
        '/^byOwner\/(.*?)$/' => 'byOwner',
        '/^bySeverity\/(.*?)$/' => 'bySeverity',
        '/^byMilestone\/(.*?)$/' => 'byMilestone',
        '/^byStatus\/(.*?)$/' => 'byStatus',
    );

    /**
     * Load this metric
     *
     * @return void
     * @throws Metric_Artifact_Defects_Total_InvalidClass
     **/
    public function reload()
    {
        // go to the metric required
        foreach (new RegexIterator(new ArrayIterator($this->_patterns), '/^by\w+$/') as $pattern) {
            if (is_null($this->_getOption($pattern)))
                continue;

            $method = '_reload' . ucfirst($pattern);
            if (!method_exists($this, $method)) {
                FaZend_Exception::raise(
                    'Metric_Artifact_Defects_Total_InvalidClass',
                    "Method '$method' is not implemented"
                );
            }
                    
            $this->$method($this->_getOption($pattern));
        }
        
        // total amount of tickets in the project
        $this->_value = count($this->_retrieveBy());
    }
    
    /**
     * Reload the metric by the given reporter
     *
     * @param string Reporter's email
     * @return void
     **/
    protected function _reloadByReporter($reporter) 
    {
        $this->_value = count($this->_retrieveBy(array('reporter'=>$reporter)));
    }
        
    /**
     * Reload the metric by the given owner
     *
     * @param string Owner's email
     * @return void
     **/
    protected function _reloadByOwner($owner) 
    {
        $this->_value = count($this->_retrieveBy(array('owner'=>$owner)));
    }
        
    /**
     * Reload the metric by the given component
     *
     * @param string Name of the component
     * @return void
     **/
    protected function _reloadByComponent($component) 
    {
        $this->_value = count($this->_retrieveBy(array('component'=>$component)));
    }
        
    /**
     * Reload the metric by the given milestone
     *
     * @param string Name of milestone
     * @return void
     **/
    protected function _reloadByMilestone($milestone) 
    {
        $this->_value = count($this->_retrieveBy(array('milestone'=>$milestone)));
    }
        
    /**
     * Reload the metric by the given severity
     *
     * @param string Name of severity
     * @return void
     **/
    protected function _reloadBySeverity($severity) 
    {
        $this->_value = count($this->_retrieveBy(array('severity'=>$severity)));
    }
        
    /**
     * Reload the metric by the given status
     *
     * @param string Name of the status
     * @return void
     **/
    protected function _reloadByStatus($status) 
    {
        $this->_value = count($this->_retrieveBy(array('status'=>$status)));
    }
        
    /**
     * Retrieve project tickets by the selection
     *
     * @param string Query to run
     * @return void
     */
    protected function _retrieveBy(array $query = array()) 
    {
        return $this->_project
            ->fzProject()
            ->getAsset(Model_Project::ASSET_DEFECTS)
            ->retrieveBy($query);
    }
        
    /**
     * Get work package
     *
     * @param string[] Names of metrics, to consider after this one
     * @return theWorkPackage
     **/
    protected function _derive(array &$metrics = array())
    {
        $component = $this->_getOption('byComponent');
        switch ($component) {
            case 'SRS':
                return $this->_makeWp(
                    $this->_project->metrics['artifacts/requirements/functional/total']->delta * 10, 
                    'To find defects in SRS'
                );
        
            case 'Design':
                return $this->_makeWp(
                    $this->_project->metrics['artifacts/design/classes/total']->delta * 10, 
                    'To find defects in Design'
                );
        
            case null:
                $metrics = array(
                    'artifacts/defects/total/byComponent/SRS',
                    'artifacts/defects/total/byComponent/Design'
                );
                return null;
        }
    }
        
}
