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

require_once 'artifacts/ProjectRegistry/Project/Time/ActivityList/slice-plugins/Abstract.php';

/**
 * Resolve milestones by finding their predecessors and interlinking them
 * 
 * @package Slice_Plugin
 */
class Slice_Plugin_ResolveMilestones extends Slice_Plugin_Abstract {

    /**
     * Resolve milestones
     *
     * @return $this
     **/
    public function execute() {
        foreach ($this as $activity) {
            if ($activity->isMilestone())
                $this->_addPredecessors($activity);
        }
    }
        
    /**
     * Add predecessors to this milestone
     *
     * @param theMilestone The milestone to resolve
     * @return void
     **/
    protected function _addPredecessors(theMilestone $milestone) {
        return ;
        /**
         * We retrieve an array of metric codes for this milestone.
         * There are all metrics, that impact this milestone's criteria.
         * For example:
         * <code>
         *   (artifacts/requirements/functional/total, artifacts/defects/total)
         * </code>
         */
        $metrics = $this->_getKidMetrics($milestone);
        if (!count($metrics)) {
            FaZend_Exception::raise('ResolveMilestoneCantResolve',
                "Milestone '{$milestone}' can't be resolved, because no metrics impact it!");
        }
        
        /**
         * We build a matrix where vertically goes metrics/work packages
         * and horizontally activities+values for them. Key is a full name of
         * activity and value is the value of metric produced by this activity.
         * For example:
         * <code>
         *   requirements/functional/total => (requ..a1=>3, requ..a2=>8)
         *   defects/total => (defects.total.a0=>120, defects.total.a1=>140)
         * </code>
         * The matrix is filtered by given set of metrics. Everything that
         * is outside of the provided list of metrics - goes out.
         */
        $matrix = $this->_getMatrix($metrics);
        
        /**
         * Here we have to make sure that every line (metric/WP) is sorted
         * in "order of impact", meaning that values moving us OUT of target
         * are located left and values moving us TORWARDS the target are
         * located right.
         */
        $this->_orderByImpact($matrix, $milestone);
        
        /**
         * Maybe this matrix is not valid? Meaning that there is NO
         * option to make milestone criteria TRUE with the values
         * from the matrix.
         */
        if (!$this->_isMatrixPositive($matrix, $milestone)) {
            FaZend_Exception::raise('ResolveMilestoneCantResolve',
                "Milestone '{$milestone}' can't be resolved, it's never positive!");
        }
                
        /**
         * Find the most close "vertical" combination where milestone is positive
         */
        $positionNew = array();
        do {
            $position = $positionNew;
            $positionNew = $this->_getNewPosition($matrix, $milestone, $position);
        } while ($positionNew != $position);
        
        /**
         * Make sure all of found activities are predecessors of this
         * particular milestone.
         */
        foreach (array_keys($position) as $code) {
            $milestone->predecessors->add($this->_findByName($name));
        }
        
    }

    /**
     * Get a list of metrics that impact this given milestone
     *
     * @param theMilestone The milestone to compare with
     * @return array List of metric names
     **/
    protected function _getKidMetrics(theMilestone $milestone) {
        return $milestone->criteria->getAffectors();
    }

    /**
     * Build matrix for the given list of metrics
     *
     * @param array List of metrics
     * @return array Matrix to work with
     **/
    protected function _getMatrix(array $metrics) {
        return $metrics;
    }
    
    /**
     * Order activities and their values by the impact they have
     *
     * @param array Matrix of activities, values and metric names
     * @param theMilestone The milestone to compare with
     * @return void
     **/
    protected function _orderByImpact(array &$matrix, theMilestone $milestone) {
    }
        
    /**
     * Is matrix positive at this position?
     *
     * @param array Matrix of activities, values and metric names
     * @param theMilestone The milestone to compare with
     * @param array Collection of metric names and activity codes (key=>value)
     * @return boolean
     **/
    protected function _isMatrixPositive(array $matrix, theMilestone $milestone, array $position = array()) {
        return false;
    }
        
    /**
     * Find and return new position, using the given one
     *
     * @param array Matrix of activities, values and metric names
     * @param theMilestone The milestone to compare with
     * @param array Collection of metric names and activity codes (key=>value)
     * @return array New position
     **/
    protected function _getNewPosition(array $matrix, theMilestone $milestone, array $position) {
        return $position;
    }
        
}
