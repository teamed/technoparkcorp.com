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

/**
 * Total number of defects, found
 * 
 * @package Artifacts
 */
class Metric_Artifacts_Defects_Found extends Metric_Abstract
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
     */
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
        $this->value = count($this->_retrieveBy());
    }
    
    /**
     * Reload the metric by the given reporter
     *
     * @param string Reporter's email
     * @return void
     */
    protected function _reloadByReporter($reporter) 
    {
        $this->value = count($this->_retrieveBy(array('reporter'=>$reporter)));
    }
        
    /**
     * Reload the metric by the given owner
     *
     * @param string Owner's email
     * @return void
     */
    protected function _reloadByOwner($owner) 
    {
        $this->value = count($this->_retrieveBy(array('owner'=>$owner)));
    }
        
    /**
     * Reload the metric by the given component
     *
     * @param string Name of the component
     * @return void
     * @throws Metric_Artifacts_Defects_Found_ComponentNotFoundException
     */
    protected function _reloadByComponent($component) 
    {
        $this->value = count($this->_retrieveBy(array('component'=>$component)));
        switch (true) {
            case $component == 'SRS':
                $this->default = round(
                    $this->_project->metrics['artifacts/requirements/functional/accepted']->objective
                    * $this->_project->metrics['history/ratios/defects/per/functional']->value
                );
                break;
            case $component == 'Design':
                $this->default = round(
                    $this->_project->metrics['artifacts/design/classes/accepted']->objective
                    * $this->_project->metrics['history/ratios/defects/per/class']->value
                );
                break;
            case $component == 'QOS':
                $this->default = round(
                    $this->_project->metrics['artifacts/requirements/qos/accepted']->objective
                    * $this->_project->metrics['history/ratios/defects/per/qos']->value
                );
                break;
            case preg_match('/^R\d/', $component):
                $this->default = round(
                    $this->_project->metrics['artifacts/defects/found/byComponent/SRS']->objective
                    * $this->_project->metrics['history/ratios/defects/per/functional-first']->value,
                    -1
                );
                break;
            default: 
                FaZend_Exception::raise(
                    'Metric_Artifacts_Defects_Found_ComponentNotFoundException',
                    "Can't find what is '{$component}' component"
                );
                break;
        }
    }
        
    /**
     * Reload the metric by the given milestone
     *
     * @param string Name of milestone
     * @return void
     */
    protected function _reloadByMilestone($milestone) 
    {
        $this->value = count($this->_retrieveBy(array('milestone'=>$milestone)));
    }
        
    /**
     * Reload the metric by the given severity
     *
     * @param string Name of severity
     * @return void
     */
    protected function _reloadBySeverity($severity) 
    {
        $this->value = count($this->_retrieveBy(array('severity'=>$severity)));
    }
        
    /**
     * Reload the metric by the given status
     *
     * @param string Name of the status
     * @return void
     */
    protected function _reloadByStatus($status) 
    {
        $this->value = count($this->_retrieveBy(array('status'=>$status)));
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
     */
    protected function _derive(array &$metrics = array())
    {
        $component = $this->_getOption('byComponent');

        if (!$component) {
            $components = array();
            foreach ($this->_project->deliverables->functional as $requirement) {
                if ($requirement->getLevel() === 0) {
                    $components[] = strval($requirement);
                }
            }
            
            $components = array_merge($components, array('SRS', 'Design', 'QOS'));
            
            foreach ($components as $c) {
                $metrics[] = 'artifacts/defects/found/byComponent/' . $c;
            }
            return null;
        }
        
        if ($this->delta <= 0) {
            return null;
        }

        // price of one defect
        $price = new FaZend_Bo_Money(
            $this->_project->metrics['history/cost/defects/find/byComponent/' . $component]->value
        );

        return $this->_makeWp(
            $price->mul($this->delta), 
            sprintf(
                'to find +%d defects in %s',
                $this->delta,
                $component
            )
        );
    }
        
}
