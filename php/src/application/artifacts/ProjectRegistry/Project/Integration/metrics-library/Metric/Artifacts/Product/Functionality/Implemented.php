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
 * Percentage of functionality implemented
 *
 * This metric's current value is the total number of functional
 * requirements implemented so far, in relation to the total number
 * of functional requirements. By "implemented" we mean that
 * requirement is finished in implementation by a designated programmer
 * and the ticket is closed. This is realized in
 * {@link Deliverables_Requirements_Requirement_Functional::isImplemented()}.
 *
 * Target value of the metric is always 1.00.
 *
 * The difference between target and current value is used to calculate the
 * size of work package, in {@link _derive()}.
 * 
 * @package Artifacts
 */
class Metric_Artifacts_Product_Functionality_Implemented extends Metric_Abstract
{

    /**
     * How many functional requirements are implemented already, in relation to total
     *
     * Value of the metric is the percentage of functional requirements already
     * implemented by programmers, to the total amount of requirements we are going
     * to have in the project. Doesn't mean that requirements are delivered to
     * users, or approved.
     *
     * For example, we are planning to have 50 requirements, according to our
     * objectives. Now we have 10 requirements specified in SRS and 5 of them
     * are implemented. The value of this metric will be 0.1 (10%).
     *
     * @return void
     * @see theMetrics::_attachMetric()
     */
    public function reload()
    {
        $this->value = $this->_percentage(
            $this->_project->deliverables->functional,
            'isImplemented',
            $this->_project->metrics['artifacts/requirements/functional/accepted']->objective
        );
        $this->default = 1; // by default all requirements shall be implemented
    }
    
    /**
     * Get work package
     *
     * Work package derived here is about a work to be done
     * in order to implement all functional requiremnts which are not
     * implemented yet.
     *
     * @param string[] Names of metrics, to consider after this one
     * @return theWorkPackage|null
     * @todo here we don't take into account the fact that some
     *      functional requirements WERE implemented before and we
     *      should spend less time for them than for requirements that
     *      are going to be implemented for the first time
     */
    protected function _derive(array &$metrics = array())
    {
        // this is how many requirements we should implement
        $toImplement = $this->delta 
            * $this->_project->metrics['artifacts/requirements/functional/accepted']->objective;
        if ($toImplement <= 0) {
            return null;
        }
        
        // price of one functional requirement implementation
        $price = new FaZend_Bo_Money(
            $this->_project->metrics['history/cost/product/functionality/implement']->value
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
