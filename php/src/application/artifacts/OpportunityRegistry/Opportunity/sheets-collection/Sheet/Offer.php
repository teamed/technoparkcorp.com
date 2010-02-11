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
        'price' => '25 EUR',
        'deposit' => '25%',
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
            return $this->pricePerHour->mul($matches[1] * $this->sheets['ROM']->hours / 100);
        } else {
            return new FaZend_Bo_Money($this->deposit);
        }
    }

}
