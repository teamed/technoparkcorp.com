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
 * Collection of all deliverable attributes
 *
 * @package Artifacts
 */
class theDeliverableAttributes implements ArrayAccess
{
    
    /**
     * List of attributes
     *
     * @var theDeliverableAttribute[]
     */
    protected $_attribs;
    
    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct()
    {
        $this->_attribs = new ArrayIterator();
    }
    
    /**
     * Get one attribute, method for ArrayAccess interface
     *
     * @param string Name of the attribute
     * @return theDeliverableAttribute
     */
    public function offsetGet($name) 
    {
        if (!isset($this->_attribs[$name])) {
            $this->_attribs[$name] = new theDeliverableAttribute();
        }
        return $this->_attribs[$name];
    }
    
    /**
     * Attribute exits? method for ArrayAccess interface
     *
     * @param string Name of the attribute
     * @return boolean
     */
    public function offsetExists($name) 
    {
        return $this->_attribs->offsetExists($name);
    }
    
    /**
     * Set one attribute, method for ArrayAccess interface
     *
     * @param string Name of the attribute
     * @return theDeliverableAttribute
     * @throws DeliverableAttributes_WriteProhibitedException
     */
    public function offsetSet($name, $value) 
    {
        FaZend_Exception::raise(
            'DeliverableAttributes_WriteProhibitedException',
            "You can't change attributes directly"
        );
    }
    
    /**
     * Unset one attribute, method for ArrayAccess interface
     *
     * @param string Name of the attribute
     * @return theDeliverableAttribute
     * @throws DeliverableAttributes_WriteProhibitedException
     */
    public function offsetUnset($name) 
    {
        FaZend_Exception::raise(
            'DeliverableAttributes_WriteProhibitedException',
            "You can't change attributes directly"
        );
    }
    
}
