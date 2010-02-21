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
 * @version $Id: Deliverables.php 716 2010-02-21 14:20:35Z yegor256@yahoo.com $
 *
 */

/**
 * One attribute of one deliverable
 *
 * @package Artifacts
 */
class theDeliverableAttribute
{
    
    /**
     * Value
     *
     * @var mixed
     */
    protected $_value;
    
    /**
     * When this attribute was set
     *
     * @var Zend_Date
     */
    protected $_date;
    
    /**
     * Log about this attribute, if any
     *
     * @var string
     */
    protected $_log;
    
    /**
     * Construct the class
     *
     * @param mixed Value
     * @param Zend_Date When this value was set?
     * @param string Log, if necessary
     * @return void
     */
    public function __construct($value, Zend_Date $date, $log)
    {
        $this->_value = $value;
        $this->_date = $date;
        $this->_log = $log;
    }
    
    /**
     * Get value
     *
     * @return string
     */
    public function __toString() 
    {
        return strval($this->_value);
    }
    
}
