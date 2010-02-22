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
 * @version $Id: Deliverables.php 716 2010-02-21 14:20:35Z yegor256@yahoo.com $
 *
 */

/**
 * One value of one attribute of one deliverable
 *
 * @package Artifacts
 */
class theDeliverableAttributeValue
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
     * Who changed, email?
     *
     * @var string
     */
    protected $_author;
    
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
     * @param string Email of author of changes
     * @param string Log, if necessary
     * @return void
     */
    public function __construct($value, Zend_Date $date, $author, $log)
    {
        $this->_value = $value;
        $this->_date = $date;
        $this->_author = $author;
        $this->_log = $log;
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string|array
     * @throws DeliverableAttributeValue_InvalidPropertyException
     */
    public function __get($name)
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
            
        $var = '_' . $name;
        if (property_exists($this, $var)) {
            return $this->$var;
        }
        
        FaZend_Exception::raise(
            'DeliverableAttributeValue_InvalidPropertyException', 
            "Can't find what is '{$name}' in " . get_class($this)
        );        
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
