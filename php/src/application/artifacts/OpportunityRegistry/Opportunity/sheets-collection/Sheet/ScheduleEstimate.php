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
 * @version $Id: RiskAnalysis.php 729 2010-02-22 12:06:48Z yegor256@yahoo.com $
 *
 */

require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Abstract.php';

/**
 * Preliminary schedule estimate
 *
 * @package Artifacts
 */
class Sheet_ScheduleEstimate extends Sheet_Abstract
{
    
    /**
     * Defaults
     *
     * @var array
     * @see __get()
     */
    protected $_defaults = array(
        'works' => array(),
    );

    /**
     * Work packages to be done
     *
     * @var Sheet_Schedule_Package[]
     */
    protected $_packages;

    /**
     * Initialize the class
     *
     * @return void
     */
    protected function _init() 
    {
        // dependency injection
        Sheet_ScheduleEstimate_Package_Abstract::setSheet($this);
        
        $this->_packages = array();
        foreach ($this->works as $work) {
            $params = array();
            foreach ($work as $item) {
                $params[strval($item['name'])] = strval($item['value']);
            }
            Sheet_ScheduleEstimate_Package_Abstract::factory(
                strval($work['name']),
                strval($work['value']),
                $params,
                $this->_packages
            );
        }
    }

    /**
     * Builds and returns Gantt Chart in TeX
     *
     * @return string
     */
    public function getChart() 
    {
        $chart = new Sheet_ScheduleEstimate_Chart();
        
        $packages = count($this->_packages);
        $chart->setOptions(
            array(
                'width' => 14, // cm
                'height' => $packages * 0.8, // cm
                'useAccuracy' => false,
            )
        );
        
        $chart->setXScale(
            30,
            'sprintf("%s", intval(${a1}/30))'
        );
        
        foreach ($this->_packages as $package) {
            $package->addYourself($chart);
        }
        return $chart->getLatex($this->sheets->getView());
    }

}
