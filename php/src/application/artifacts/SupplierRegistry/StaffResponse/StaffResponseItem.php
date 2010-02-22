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

/**
 * One response for staff
 *
 * @package Artifacts
 */
class theStaffResponseItem
{

    /**
     * Supplier suggested
     *
     * @var theSupplier
     */
    protected $_supplier;
    
    /**
     * Quality of this supplier (0..100)
     *
     * @var integer
     */
    protected $_quality;
    
    /**
     * Explanation of this choice
     *
     * @var string
     */
    protected $_reason;
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     **/
    public function __get($name) 
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise(
            'StaffResponseItem_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
    /**
     * Set supplier
     *
     * @param theSupplier
     * @return void
     **/
    public function setSupplier(theSupplier $supplier) 
    {
        $this->_supplier = $supplier;
        return $this;
    }

    /**
     * Set quality
     *
     * @param integer Quality
     * @return void
     **/
    public function setQuality($quality) 
    {
        validate()
            ->type($quality, 'integer', "Quality must be INTEGER")
            ->true($quality <= 100 && $quality >= 0, 
                "Quality must be in [0..100] interval, {$quality} provided");
        $this->_quality = $quality;
        return $this;
    }

    /**
     * Set reason
     *
     * @param string Reason
     * @return void
     **/
    public function setReason($reason) 
    {
        $this->_reason = $reason;
        return $this;
    }

}
