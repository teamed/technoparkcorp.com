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
 * @version $Id: Implemented.php 884 2010-03-31 17:35:38Z yegor256@yahoo.com $
 *
 */

/**
 * Total number of puzzles in code
 *
 * @package Artifacts
 */
class Metric_Artifacts_Product_Functionality_Puzzles extends Metric_Abstract
{

    /**
     * How many puzzles we have now?
     *
     * @return void
     * @see theMetrics::_attachMetric()
     */
    public function reload()
    {
        $this->value = 10;
        $this->default = 0; // we should have it ZERO when project is finished
    }
    
    /**
     * Get work package
     *
     * Work package derived here is about a work to be done
     * in order to solve all puzzles in the code.
     *
     * @param string[] Names of metrics, to consider after this one
     * @return theWorkPackage|null
     */
    protected function _derive(array &$metrics = array())
    {
        // this is how many requirements we should implement
        $toResolve = $this->objective;
        if ($toResolve <= 0) {
            return null;
        }
        
        // price of one puzzle to resolve
        $price = new FaZend_Bo_Money(
            $this->_project->metrics['history/cost/product/puzzle']->value
        );

        return $this->_makeWp(
            $price->mul($toResolve), 
            sprintf(
                'to resolve +%d puzzles',
                $toResolve
            )
        );
    }
        
}
