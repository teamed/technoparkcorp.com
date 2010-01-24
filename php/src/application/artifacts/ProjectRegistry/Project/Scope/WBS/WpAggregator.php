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
 * Aggregator of other work packages
 *
 * @package Artifacts
 */
class theWpAggregator extends theWorkPackage {

    const MAX_PACKAGES_TO_SHOW_IN_TITLE = 8;

    /**
     * List of aggregated work packages
     *
     * @var theWorkPackage
     */
    protected $_aggregatedWps = array();
    
    /**
     * Getter
     *
     * @return mixed
     **/
    public function __get($name) {
        switch ($name) {
            case 'cost':
                return $this->_getCost();
            case 'title':
                return $this->_getTitle();
        }
        return parent::__get($name);
    }
    
    /**
     * Split work package to activities
     *
     * @return void
     **/
    public function split(theActivities $list) {
        // don't split anything
    }
    
    /**
     * Add one more work package to the aggregator
     *
     * @return void
     **/
    public function addWorkPackage(theWorkPackage $wp) {
        $this->_aggregatedWps[$wp->code] = $wp;
        $this->_cost = null;
    }
    
    /**
     * Calculate and return cost
     *
     * @return FaZend_Bo_Money
     **/
    protected function _getCost() {
        if (is_null($this->_cost)) {
            $this->_cost = new FaZend_Bo_Money();
            foreach ($this->_aggregatedWps as $wp)
                $this->_cost->add($wp->cost);
        }
        return $this->_cost;
    }

    /**
     * Calculate and return title of this aggregator
     *
     * @return string
     **/
    protected function _getTitle() {
        $cnt = count($this->_aggregatedWps);
        return plural("{$cnt} Work Package[s]: ", $cnt) . 
            implode(', ', array_slice(array_keys($this->_aggregatedWps), 0, self::MAX_PACKAGES_TO_SHOW_IN_TITLE)) . 
            ($cnt > self::MAX_PACKAGES_TO_SHOW_IN_TITLE ? ', ...' : false);
    }

}
