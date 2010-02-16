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
 * Cost of one functional requirement
 * 
 * @package Artifacts
 */
class Metric_History_Cost_Requirements_Functional extends Metric_Abstract
{

    /**
     * Forwarders
     *
     * @var array
     * @see Metric_Abstract::$_patterns
     */
    protected $_patterns = array(
        '/level\/(\w+)/' => 'level',
    );

    /**
     * Price per each requirement on some level, in USD
     *
     * @var array
     */
    protected $_pricePerRequirement = array(
        'first' => 45,
        'second' => 10,
        'third' => 4,
        'forth' => 2,
    );

    /**
     * Load this metric
     *
     * @return void
     **/
    public function reload()
    {
        if (!$this->_getOption('level')) {
            $this->value = round(
                array_sum($this->_pricePerRequirement) / count($this->_pricePerRequirement)
            );
            return null;
        }
            
        $this->value = $this->_pricePerRequirement[$this->_getOption('level')];
    }
            
}
