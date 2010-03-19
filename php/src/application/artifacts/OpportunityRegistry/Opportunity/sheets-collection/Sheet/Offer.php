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

require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/Sheet/Abstract.php';

/**
 * Offer we're giving to the customer
 *
 * @package Artifacts
 */
class Sheet_Offer extends Sheet_Abstract
{

    /**
     * Defaults
     *
     * @var array
     * @see __get()
     */
    protected $_defaults = array(
        'price'      => '25 EUR',
        'deposit'    => '25%',
        'low'        => false,
        'high'       => false,
        'objectives' => array(),
        'intro'      => array(),
        'months'     => false, // duration
    );
    
    /**
     * Get name of the template file, like "Vision.tex", "ROM.tex", etc.
     *
     * @return string
     */
    public function getTemplateFile() 
    {
        return null;
    }
    
    /**
     * Get price per hour
     *
     * @return FaZend_Bo_Money
     */
    protected function _getPricePerHour() 
    {
        return new FaZend_Bo_Money($this->price);
    }

    /**
     * Get price per hour
     *
     * @return FaZend_Bo_Money
     */
    protected function _getDepositAmount() 
    {
        if (preg_match('/^(\d+(?:\.\d+)?)\%$/', $this->deposit, $matches)) {
            $amount = clone $this->lowAmount;
            return $amount
                ->mul($matches[1] / 100)
                ->round(-2);
        } else {
            return new FaZend_Bo_Money($this->deposit);
        }
    }
    
    /**
     * Get estimation ratio, relation between HIGH and LOW
     *
     * @return float
     */
    protected function _getRatio() 
    {
        $high = clone $this->highAmount;
        return $high->div($this->lowAmount);
    }
    
    /**
     * Get lower amount
     *
     * @return FaZend_Bo_Money
     * @throws Sheet_Offer_InsufficientDataException
     */
    protected function _getLowAmount() 
    {
        if ($this->low) {
            $hours = $this->low;
        } else {
            if (!isset($this->sheets['ROM'])) {
                FaZend_Exception::raise(
                    'Sheet_Offer_InsufficientDataException',
                    "Can't find 'low' in 'Offer' and 'ROM' is absent, how to calculate size?"
                );
            }
            $hours = $this->sheets['ROM']->lowBoundary;
        }
        $amount = clone $this->pricePerHour;
        return $amount->mul($hours);
    }

    /**
     * Get higher amount
     *
     * @return FaZend_Bo_Money
     * @throws Sheet_Offer_InsufficientDataException
     */
    protected function _getHighAmount() 
    {
        if ($this->high) {
            $hours = $this->high;
        } else {
            if (!isset($this->sheets['ROM'])) {
                FaZend_Exception::raise(
                    'Sheet_Offer_InsufficientDataException',
                    "Can't find 'high' in 'Offer' and 'ROM' is absent, how to calculate size?"
                );
            }
            $hours = $this->sheets['ROM']->highBoundary;
        }
        $amount = clone $this->pricePerHour;
        return $amount->mul($hours);
    }

    /**
     * Get project duration in months
     *
     * @return float
     * @throws Sheet_Offer_InsufficientDataException
     */
    protected function _getDuration() 
    {
        if ($this->months) {
            return $this->months;
        }

        if (!isset($this->sheets['ROM'])) {
            FaZend_Exception::raise(
                'Sheet_Offer_InsufficientDataException',
                "Can't find 'months' in 'Offer' and 'ROM' is absent, how to calculate duration?"
            );
        }
        return $this->sheets['ROM']->tdev;
    }
    
}
