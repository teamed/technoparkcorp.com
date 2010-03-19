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
        
        // make sure that the total cost of all packages
        // equal to the project "lowAmount", from "Offer"
        $this->_adjustCost($this->_packages);

        // make sure that the total duration of all packages
        // equal to the project "duration", from "Offer"
        $this->_adjustDuration($this->_packages);
    }

    /**
     * Restore object after serialization
     *
     * @return void
     */
    public function __wakeup() 
    {
        parent::__wakeup();
        // dependency injection
        Sheet_ScheduleEstimate_Package_Abstract::setSheet($this);
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
                'height' => $packages * 0.7, // cm
                'useAccuracy' => true,
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

    /**
     * Adjust cost
     *
     * @param array List of packages
     * @return void
     * @throws Sheet_ScheduleEstimate_CostOverrunException
     * @throws Sheet_ScheduleEstimate_CostInsufficientException
     */
    protected function _adjustCost(array $packages) 
    {
        $cost = new FaZend_Bo_Money();
        foreach ($packages as $package) {
            if ($package instanceof Sheet_ScheduleEstimate_Package_Milestone) {
                $cost->add($package->cost);
            }
        }
        $totalCost = clone $this->_sheets['Offer']->lowAmount;
        if ($cost->isGreater($totalCost)) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_CostOverrunException', 
                "Total cost of {$cost} is bigger than {$totalCost}"
            );
        }
        if ($cost->isLess($totalCost)) {
            for ($i=count($packages)-1; $i>=0; $i--) {
                $package = $packages[$i];
                if ($package instanceof Sheet_ScheduleEstimate_Package_Milestone) {
                    $package->cost->add($totalCost->sub($cost)->inverse());
                    $adjusted = true;
                    break;
                }
            }
        }
        if (!isset($adjusted) && !$cost->equalsTo($totalCost)) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_CostInsufficientException', 
                "Total cost of {$cost} is less than {$totalCost}, and can't be adjusted"
            );
        }
    }

    /**
     * Adjust duration
     *
     * @param array List of packages
     * @return void
     * @throws Sheet_ScheduleEstimate_DurationOverrunException
     * @throws Sheet_ScheduleEstimate_DurationInsufficientException
     */
    protected function _adjustDuration(array $packages) 
    {
        $duration = 0;
        foreach ($packages as $package) {
            if ($package instanceof Sheet_ScheduleEstimate_Package_Bar) {
                $duration += $package->duration;
            }
        }
        $totalDuration = $this->_sheets['Offer']->duration * 30;
        if ($duration > $totalDuration) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_DurationOverrunException', 
                "Total duration of {$duration}days is bigger than {$totalDuration}days"
            );
        }
        if ($duration < $totalDuration) {
            for ($i=count($packages)-1; $i>=0; $i--) {
                $package = $packages[$i];
                if ($package instanceof Sheet_ScheduleEstimate_Package_Bar) {
                    $package->setDuration($package->duration + $totalDuration - $duration);
                    $adjusted = true;
                    break;
                }
            }
        }
        if (!isset($adjusted) && ($duration != $totalDuration)) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_DurationInsufficientException', 
                "Total duration of {$duration} is less than {$totalDuration}, and can't be adjusted"
            );
        }
    }

}
