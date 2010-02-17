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
 * Total amount of functionality implemented
 * 
 * @package Artifacts
 */
class Metric_Artifacts_Product_Functionality_Implemented extends Metric_Abstract
{

    /**
     * Forwarders
     *
     * @var array
     * @see Metric_Abstract::$_patterns
     */
    protected $_patterns = array(
        '/^R(\d+(?:\.[\d\w]+)*)$/' => 'requirement',
    );

    /**
     * Load this metric
     *
     * @return void
     * @throws Metric_Artifact_Defects_Total_InvalidClass
     **/
    public function reload()
    {
        if ($this->_getOption('requirement')) {
            $this->value = rand(0, 1);
            return;
        }
        
        // to avoid division by zero
        if (!count($this->_project->deliverables->functional)) {
            $this->value = 0;
            return;
        }
        
        $implemented = array();
        foreach ($this->_project->deliverables->functional as $r) {
            if ($this->_project->metrics['artifacts/product/functionality/implemented/' . $r->name]->value) {
                $implemented[] = $r->name;
            }
        }
        $this->value = round(count($implemented) / count($this->_project->deliverables->functional), 2);
    }
    
    /**
     * Get work package
     *
     * @param string[] Names of metrics, to consider after this one
     * @return theWorkPackage
     **/
    protected function _derive(array &$metrics = array())
    {
        if ($this->_getOption('requirement')) {
            return null;
        }
        
        $toImplement = $this->_project->metrics['artifacts/requirements/functional/total']->delta;
        
        if ($toImplement <= 0) {
            return null
        }
        
        // price of one glossary item
        $price = new FaZend_Bo_Money(
            $this->_project->metrics['history/cost/product/functional']->value
        );

        return $this->_makeWp(
            $price->mul($toImplement), 
            sprintf(
                'to implement +%d functional requirements',
                $toImplement
            )
        );
    }
        
}
