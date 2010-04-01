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
 * @version $Id: Implemented.php 882 2010-03-31 16:27:01Z yegor256@yahoo.com $
 *
 */

/**
 * Percentage of functionality accepted by end-users (and other stakeholders)
 *
 * @package Artifacts
 * @see Metric_Artifacts_Product_Functionality_Implemented
 */
class Metric_Artifacts_Product_Functionality_Accepted extends Metric_Abstract
{

    /**
     * How big percentage of functional requirements is accepted
     *
     * Value of the metric is the percentage of functional requirements already
     * accepted by end-users. This is a relation between currently accepted
     * requirements and total number of requirements we're expecting to see
     * in the SRS.
     *
     * @return void
     * @see theMetrics::_attachMetric()
     */
    public function reload()
    {
        $this->value = $this->_percentage(
            $this->_project->deliverables->functional,
            'isAccepted',
            $this->_project->metrics['artifacts/requirements/functional/accepted']->objective            
        );
        $this->default = 1; // all of them should be accepted
    }
    
    /**
     * Get work package
     *
     * Work package derived here is about a work to be done
     * in order to accept all functional requiremnts which are not
     * accepted yet.
     *
     * @param string[] Names of metrics, to consider after this one
     * @return theWorkPackage|null
     * @todo here we don't take into account the fact that some
     *      functional requirements WERE accepted before and we
     *      should spend less time for them than for requirements that
     *      are going to be accepted for the first time
     */
    protected function _derive(array &$metrics = array())
    {
        // this is how many requirements we should implement
        $toAccept = $this->delta
            * $this->_project->metrics['artifacts/requirements/functional/accepted']->objective;
        if ($toAccept <= 0) {
            return null;
        }
        
        // price of one functional requirement acceptance
        $price = new FaZend_Bo_Money(
            $this->_project->metrics['history/cost/product/functionality/accept']->value
        );

        return $this->_makeWp(
            $price->mul($toAccept), 
            sprintf(
                'to accept +%d functional requirements',
                $toAccept
            )
        );
    }
        
}
