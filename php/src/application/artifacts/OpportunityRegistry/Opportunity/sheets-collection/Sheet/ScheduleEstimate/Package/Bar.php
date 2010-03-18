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
 * @version $Id: Abstract.php 830 2010-03-11 15:48:07Z yegor256@yahoo.com $
 *
 */

require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/' .
    'Sheet/ScheduleEstimate/Package/Abstract.php';

/**
 * One bar of work in the chart
 *
 * @package Artifacts
 */
class Sheet_ScheduleEstimate_Package_Bar extends Sheet_ScheduleEstimate_Package_Abstract
{
    
    /**
     * Duration of the bar, in days
     *
     * @var integer
     */
    protected $_duration;
    
    /**
     * Add package to the chart
     *
     * @param Sheet_ScheduleEstimate_Chart Chart to use
     * @return void
     */
    public function addYourself(Sheet_ScheduleEstimate_Chart $chart)
    {
        $chart->addBar(
            $this->_name,
            $this->_duration,
            $this->_comment,
            $this->_accuracy
        );
        parent::addYourself($chart);
    }
    
    /**
     * Set duration of the bar
     *
     * @param string Could be "45 days" or "4 months" or "4%" or "10% of Elaboration"
     * @return $this
     */
    public function setDuration($duration) 
    {
        switch (true) {
            // just a number, it's in days
            case is_numeric($duration):
                $this->_duration = intval($duration);
                break;

            // "54 days"
            case preg_match('/^(\d+)\s?day(?:s)?$/i', $duration, $matches):
                $this->_duration = intval($matches[1]);
                break;

            // "4 months"
            case preg_match('/^(\d+)\s?mon(?:ths)?$/i', $duration, $matches):
                $this->_duration = intval($matches[1]) * 30;
                break;

            // "13 weeks"
            case preg_match('/^(\d+)\s?week(?:s)?$/i', $duration, $matches):
                $this->_duration = intval($matches[1]) * 7;
                break;

            // "13%"
            case preg_match('/^(\d+(?:\.\d+)?)%$/', $duration, $matches):
                $this->_duration = self::$_sheet->sheets['Offer']->duration * 30 *
                    intval($matches[1]) / 100;
                break;

            // something unknown
            default:
                FaZend_Exception::raise(
                    'Sheet_ScheduleEstimate_Package_Bar_SyntaxException', 
                    "Can't understand duration: '{$duration}'"
                );
        }
        return $this;
    }

}
