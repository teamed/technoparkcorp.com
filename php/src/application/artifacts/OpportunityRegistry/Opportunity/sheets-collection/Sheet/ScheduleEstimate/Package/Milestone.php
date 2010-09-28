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
 * One milestone in the chart
 *
 * @package Artifacts
 */
class Sheet_ScheduleEstimate_Package_Milestone extends Sheet_ScheduleEstimate_Package_Abstract
{
    
    /**
     * Cost of the bar, in USD
     *
     * @var FaZend_Bo_Money
     */
    protected $_cost;
    
    /**
     * Set cost of the bar
     *
     * @param string Could be "45%" or "450 USD" or "deposit" or "24% of Elaboration"
     * @return $this
     * @throws Sheet_ScheduleEstimate_Package_Milestone_SyntaxException
     */
    public function setCost($cost) 
    {
        switch (true) {
            // full deposit amount
            case $cost == 'deposit':
                $this->_cost = clone self::$_sheet->sheets['Offer']->depositAmount;
                break;
                
            // percents, like "5%" or "15.4%"
            case preg_match('/^(\d+(?:\.\d+)?)%$/', $cost, $matches):
                $this->_cost = FaZend_Bo_Money::factory(self::$_sheet->sheets['Offer']->fixedAmount)
                    ->mul(intval($matches[1])/100);
                break;
                
            // maybe it's an absolute amount of money, like "500 EUR"
            case true:
                try {
                    $this->_cost = FaZend_Bo_Money::factory($cost);
                    break;
                } catch (FaZend_Bo_Money_InvalidFormat $e) {
                    // ignore
                }
                // no "break" here, since we should continue

            // something unknown
            default:
                FaZend_Exception::raise(
                    'Sheet_ScheduleEstimate_Package_Milestone_SyntaxException', 
                    "Can't understand cost: '{$cost}'"
                );
        }
        return $this;
    }

    /**
     * Add milestone to the chart
     *
     * @param Sheet_ScheduleEstimate_Chart Chart to use
     * @return void
     */
    public function addYourself(Sheet_ScheduleEstimate_Chart $chart)
    {
        if (isset($this->_cost)) {
            $chart->addBar(
                $this->_name, // name
                0, // size, should be ZERO, since it's milestone!
                self::_scaleCost($this->_cost), // comment
                1, // accuracy
                $this->_accuracy != 1 ? 
                self::_scaleCost($this->_cost, $this->_accuracy) : false // worst comment
            );
        } else {
            $chart->addBar(
                $this->_name, // name
                0, // size, should be ZERO, since it's milestone!
                $this->_name // comment
            );
        }
        parent::addYourself($chart);
    }
    
    /**
     * Scale cost to the best string presentation
     *
     * @param FaZend_Bo_Money Cost to scale
     * @param float Accuracy to use, if necessary
     * @return string
     * @see addYourself()
     * @todo implement it properly
     */
    protected static function _scaleCost(FaZend_Bo_Money $cost, $accuracy = 1) 
    {
        $total = self::$_sheet->sheets['Offer']->highAmount->original;
        $val = $cost->original * $accuracy;
        
        return $cost->currency->getSymbol() . round($val);
    }

}
