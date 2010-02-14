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

require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Abstract.php';

/**
 * Vision
 *
 * @package Artifacts
 */
class Sheet_ROM extends Sheet_Abstract
{
    
    /**
     * Defaults
     *
     * @var array
     * @see __get()
     */
    protected $_defaults = array(
        'estimators' => array(),
    );
    
    /**
     * Estimates provided
     *
     * @var string
     */
    protected $_estimates;
    
    /**
     * Round float to the nearest valid precision
     *
     * @param float
     * @return integer
     */
    public static function round($int) 
    {
        return intval(round($int));
    }
    
    /**
     * Initialize the clas
     *
     * @return void
     */
    protected function _init() 
    {
        $this->_estimates = array();
        foreach ($this->estimators as $estimator) {
            $lines = array();
            foreach ($estimator as $item) {
                $lines[] = $item['name'] . ': ' . $item['value'];
            }
            $this->_estimates[] = Sheet_ROM_Estimate_Abstract::factory($lines)
                ->setEstimator(strval($estimator['name']))
                ->setPromo(strval($estimator['value']));
        }
    }
    
    /**
     * Average estimate
     *
     * @return integer Average estimate
     */
    protected function _getHours() 
    {
        $hours[] = array();
        foreach ($this->_estimates as $estimate) {
            $hours[] = $estimate->hours;
        }
        if (!count($hours)) {
            return 0;
        }
        return self::round(array_sum($hours) / count($hours));
    }
    
    /**
     * Get LOW boundary
     *
     * @return integer Staff-hours
     */
    protected function _getLowBoundary() 
    {
        return $this->hours;
    }
    
    /**
     * Get HIGH boundary
     *
     * @return integer Staff-hours
     */
    protected function _getHighBoundary() 
    {
        return self::round($this->hours * 1.75);
    }
    
    /**
     * Total amount of estimators
     *
     * @return integer
     */
    protected function _getTotal() 
    {
        return count($this->_estimates);
    }
    
}
